<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Stevebauman\Location\Facades\Location;

class RegistrarAcesso
{
    /**
     * Handle an incoming request.
     *
     * Registra o acesso de visitantes não autenticados na tabela visitor_logs
     * usando o pacote stevebauman/location para geolocalização via IP.
     * 
     * Visitantes não cadastrados têm seus dados retidos por 90 dias
     * (conforme política de privacidade e LGPD).
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Primeiro, deixa o Laravel processar a requisição e gerar a resposta
        $response = $next($request);

        // Registra apenas visitantes NÃO autenticados (usuários logados vão para historico_acessos)
        if (!$request->user()) {
            try {
                $ip = $request->ip();
                $position = null;

                // Tenta obter localização via stevebauman/location
                try {
                    if ($ip && $ip !== '127.0.0.1' && $ip !== '::1') {
                        $position = Location::get($ip);
                    }
                } catch (\Exception $e) {
                    // Falha na geolocalização não deve travar o registro
                    \Log::debug('Location lookup failed for IP: ' . $ip, ['error' => $e->getMessage()]);
                }

                VisitorLog::create([
                    'ip_address'       => $ip,
                    'user_agent'       => $request->header('User-Agent'),
                    'country'          => $position?->countryName,
                    'region'           => $position?->regionName,
                    'city'             => $position?->cityName,
                    'latitude'         => $position?->latitude,
                    'longitude'        => $position?->longitude,
                    'page_url'         => $request->fullUrl(),
                    'referrer_url'     => $request->header('referer'),
                    'cookies_consented' => $request->cookie('cookie_consent') === 'accepted',
                ]);
            } catch (\Exception $e) {
                // Silencia erros de log para não travar o app se o banco falhar
                \Log::error('Visitor log registration failed', ['error' => $e->getMessage()]);
            }
        }

        return $response;
    }
}
