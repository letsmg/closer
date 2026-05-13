<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Middleware de Autenticação Híbrida
 * 
 * Este middleware detecta automaticamente o tipo de autenticação:
 * - API/Mobile (Flutter): Usa JWT Bearer Token
 * - Web: Usa Session tradicional do Laravel
 * 
 * 🔒 SEGURANÇA:
 * - Verifica token_version nos JWTs para invalidar tokens após migrate:fresh
 * - Verifica sessão ativa na tabela de sessions
 * - Garante que usuário deletado/recriado não mantenha sessão ativa
 */
class HybridAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Detecta se é requisição API (JSON ou com header específico)
        $isApiRequest = $this->isApiRequest($request);

        // Tenta autenticar com JWT primeiro (se houver token)
        if ($this->hasJwtToken($request)) {
            try {
                $user = JWTAuth::parseToken()->authenticate();

                if ($user) {
                    // Verificações adicionais para JWT
                    if (!$user->ativo) {
                        return $this->unauthorized($isApiRequest, 'Conta desativada.', 403);
                    }

                    if (!$user->hasVerifiedEmail()) {
                        return $this->unauthorized($isApiRequest, 'Email não verificado.', 403);
                    }

                    // 🔒 VALIDAÇÃO CRÍTICA: Verifica token_version
                    // Após migrate:fresh --seed, todos os usuários são recriados com token_version=1
                    // Tokens JWT antigos emitidos antes do reset terão token_version=0 ou versão antiga
                    // Isso invalida TODOS os tokens antigos automaticamente
                    $tokenVersion = (int) JWTAuth::getPayload()->get('token_version', 0);
                    if ($tokenVersion === 0 || $tokenVersion !== (int) $user->token_version) {
                        try {
                            JWTAuth::invalidate(JWTAuth::getToken());
                        } catch (\Exception $e) {
                            // Ignora erros ao invalidar
                        }
                        return $this->unauthorized(
                            $isApiRequest,
                            'Sessão expirada. Por segurança, faça login novamente.',
                            401
                        );
                    }

                    // Define o usuário como autenticado
                    Auth::setUser($user);

                    return $next($request);
                }
            } catch (TokenExpiredException $e) {
                return $this->unauthorized($isApiRequest, 'Token expirado.', 401);
            } catch (TokenInvalidException $e) {
                return $this->unauthorized($isApiRequest, 'Token inválido.', 401);
            } catch (JWTException $e) {
                // Token não presente ou erro JWT, tenta web
            }
        }

        // Tenta autenticação web (sessão)
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            // Verificações para web
            if (!$user->ativo) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($isApiRequest) {
                    return response()->json(['message' => 'Conta desativada.'], 403);
                }

                return redirect()->route('login')
                    ->with('error', 'Sua conta foi desativada.');
            }

            // 🔒 Para web sessions: ao rodar migrate:fresh a tabela sessions é recriada vazia,
            // então o cookie de sessão antigo não encontrará registro correspondente,
            // forçando o usuário a fazer login novamente.
            // O remember_token armazena um hash no banco que também será diferente após recriação.
            
            // Define o guard padrão para web
            Auth::shouldUse('web');

            return $next($request);
        }

        // Não autenticado
        return $this->unauthorized($isApiRequest, 'Não autenticado.', 401);
    }

    /**
     * Detecta se a requisição é de API
     */
    private function isApiRequest(Request $request): bool
    {
        // Verifica header Accept
        $acceptHeader = $request->header('Accept', '');

        // Verifica se pede JSON
        if (str_contains($acceptHeader, 'application/json')) {
            return true;
        }

        // Verifica header X-Requested-With (AJAX)
        if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
            return true;
        }

        // Verifica se é requisição para rotas API
        if (str_starts_with($request->path(), 'api/')) {
            return true;
        }

        // Verifica header Authorization (indica API)
        if ($request->hasHeader('Authorization')) {
            return true;
        }

        // Verifica Content-Type
        $contentType = $request->header('Content-Type', '');
        if (str_contains($contentType, 'application/json')) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se há token JWT na requisição
     */
    private function hasJwtToken(Request $request): bool
    {
        $authHeader = $request->header('Authorization', '');
        return str_starts_with($authHeader, 'Bearer ');
    }

    /**
     * Retorna resposta de não autorizado apropriada
     */
    private function unauthorized(bool $isApiRequest, string $message, int $status)
    {
        if ($isApiRequest) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], $status);
        }

        // Para web, redireciona para login
        return redirect()->guest(route('login'))
            ->with('warning', 'Por favor, faça login para continuar.');
    }
}