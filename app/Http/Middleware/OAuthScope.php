<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Middleware de Verificação de Escopos OAuth2
 * 
 * Uso: Route::middleware(['auth:api', 'scope:read:profile,write:messages'])
 * 
 * Verifica se o token JWT tem os escopos necessários para acessar a rota.
 */
class OAuthScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$scopes  Escopos necessários (separados por vírgula na definição da rota)
     */
    public function handle(Request $request, Closure $next, string ...$scopes): Response
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $tokenScopes = $payload->get('scopes', []);

            // Verifica se tem pelo menos um dos escopos necessários (OR)
            // Ou todos os escopos (AND) - configurável
            $requireAll = $request->header('X-Require-All-Scopes') === 'true';

            if ($requireAll) {
                // AND: Precisa de TODOS os escopos listados
                $missing = array_diff($scopes, $tokenScopes);
                if (!empty($missing)) {
                    return $this->forbidden('Escopos insuficientes. Faltam: ' . implode(', ', $missing));
                }
            } else {
                // OR: Precisa de PELO MENOS UM dos escopos
                $hasScope = !empty(array_intersect($scopes, $tokenScopes));
                
                if (!$hasScope) {
                    return $this->forbidden(
                        'Acesso negado. Escopos necessários: ' . implode(' ou ', $scopes)
                    );
                }
            }

            // Adiciona escopos do token ao request para uso posterior
            $request->attributes->set('token_scopes', $tokenScopes);

            return $next($request);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'invalid_token',
                'error_description' => 'Token inválido ou expirado.'
            ], 401);
        }
    }

    /**
     * Resposta de acesso negado
     */
    protected function forbidden(string $message): Response
    {
        return response()->json([
            'error' => 'insufficient_scope',
            'error_description' => $message,
        ], 403);
    }
}
