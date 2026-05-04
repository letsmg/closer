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
 * Como usar:
 * Route::middleware(['auth.hybrid'])->group(...)
 * 
 * Prioridade de verificação:
 * 1. Verifica se há header Authorization com Bearer Token (JWT)
 * 2. Se não, verifica sessão web tradicional
 * 3. Se nenhum válido, retorna erro apropriado baseado no tipo de requisição
 */
class HybridAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
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
                Auth::logout();
                
                if ($isApiRequest) {
                    return response()->json(['message' => 'Conta desativada.'], 403);
                }
                
                return redirect()->route('login')
                    ->with('error', 'Sua conta foi desativada.');
            }

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
