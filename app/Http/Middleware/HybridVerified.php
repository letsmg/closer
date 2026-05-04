<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Middleware de Verificação de Email Híbrida
 * 
 * Funciona tanto para API (JWT) quanto Web (Session)
 */
class HybridVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isApiRequest = $this->isApiRequest($request);
        
        $user = null;

        // Tenta obter usuário de JWT
        if ($this->hasJwtToken($request)) {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (JWTException $e) {
                // Ignora erro JWT, tenta web
            }
        }

        // Se não conseguiu via JWT, tenta web
        if (!$user && Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
        }

        // Verifica se usuário tem email verificado
        if (!$user || !$user->hasVerifiedEmail()) {
            if ($isApiRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email não verificado. Verifique sua caixa de entrada.',
                ], 403);
            }

            return redirect()->route('verification.notice')
                ->with('warning', 'Você precisa verificar seu email antes de continuar.');
        }

        return $next($request);
    }

    /**
     * Detecta se a requisição é de API
     */
    private function isApiRequest(Request $request): bool
    {
        $acceptHeader = $request->header('Accept', '');
        
        if (str_contains($acceptHeader, 'application/json')) {
            return true;
        }

        if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
            return true;
        }

        if ($request->hasHeader('Authorization')) {
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
}
