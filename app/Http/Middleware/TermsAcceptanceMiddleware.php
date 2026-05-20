<?php

namespace App\Http\Middleware;

use App\Models\UserTermsAcceptance;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware de validação de aceite dos Termos de Uso e Política de Privacidade
 * 
 * 🔒 VALIDAÇÃO DEFINITIVA NO BANCO DE DADOS:
 * Consulta a tabela `user_terms_acceptances` para verificar se o usuário
 * possui um aceite válido para as versões atuais dos termos.
 * 
 * Cookies/headers servem apenas como cache auxiliar de sessão,
 * mas a persistência no banco é o validador definitivo.
 * 
 * Se o aceite estiver ausente ou desatualizado (versão antiga),
 * o acesso às funcionalidades é bloqueado até novo consentimento.
 */
class TermsAcceptanceMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Rotas que não exigem aceite dos termos
        $excludedRoutes = [
            'terms',
            'privacy',
            'security',
            'robots.txt',
            'sitemap.xml',
            'logout',
            'login',
            'register',
            'api.auth.login',
            'api.auth.register',
            'api.auth.refresh',
            'oauth.token',
            'oauth.scopes',
            'terms.accept',      // Endpoint de aceite
            'terms.status',      // Endpoint de status
        ];

        $currentRoute = $request->route()?->getName();

        // Pula verificação para rotas excluídas
        if (in_array($currentRoute, $excludedRoutes)) {
            return $next($request);
        }

        // Obtém o usuário autenticado (suporta JWT e Session)
        $user = $request->user() ?? Auth::user();

        // Se não há usuário autenticado, deixa passar (outros middlewares cuidam disso)
        if (!$user) {
            return $next($request);
        }

        // Versões ativas dos termos (vindas do config)
        $requiredTermsVersion = (string) config('terms.version', '2026-05-20');
        $requiredPrivacyVersion = (string) config('terms.privacy_version', '2026-05-20');

        // 🔍 VALIDAÇÃO NO BANCO DE DADOS (validador definitivo)
        $hasValidAcceptance = UserTermsAcceptance::hasValidAcceptance(
            $user->id,
            $requiredTermsVersion,
            $requiredPrivacyVersion
        );

        if (!$hasValidAcceptance) {
            // Verifica se é requisição API (JSON)
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você precisa aceitar os Termos de Uso e a Política de Privacidade para continuar.',
                    'requires_terms_acceptance' => true,
                    'terms_version' => $requiredTermsVersion,
                    'privacy_version' => $requiredPrivacyVersion,
                    'terms_url' => url('/terms'),
                    'privacy_url' => url('/privacy'),
                ], 403);
            }

            // Para web, redireciona para página de termos
            return redirect()->to('/terms')
                ->with('warning', 'Você precisa aceitar os Termos de Uso e a Política de Privacidade para continuar.');
        }

        return $next($request);
    }
}
