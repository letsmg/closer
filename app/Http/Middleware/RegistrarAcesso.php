<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB; // IMPORTANTE: Adicione esta linha

class RegistrarAcesso
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Primeiro, deixa o Laravel processar a requisição e gerar a resposta
        $response = $next($request);

        // Depois, se o usuário estiver logado, gravamos o log
        if ($request->user()) {
            try {
                DB::table('historico_acessos')->insert([
                    'user_id'     => $request->user()->id,
                    'ip'          => $request->ip(),
                    'dispositivo' => $request->header('User-Agent'),
                    'data_hora'   => now(),
                    // Se você adicionou timestamps na migration, descomente abaixo:
                    // 'created_at' => now(),
                    // 'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Silencia erros de log para não travar o app se o banco falhar
            }
        }

        return $response;
    }
}