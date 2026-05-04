<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rate Limiting por Usuário (além do por IP)
 * 
 * Limita requisições por usuário autenticado, não apenas por IP.
 * Isso impede que usuários autenticados abusem da API mesmo mudando de IP.
 * 
 * Uso: Route::middleware(['throttle:user:60,1']) // 60 req/min por usuário
 */
class RateLimitByUser
{
    protected RateLimiter $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        // Identifica o usuário ou IP
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildResponse($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    /**
     * Resolve chave única para rate limiting
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $user = Auth::user();
        
        if ($user) {
            // Rate limit por usuário autenticado
            return 'user:' . $user->id . ':' . $request->route()->getName() ?? $request->path();
        }

        // Rate limit por IP para usuários não autenticados
        return 'ip:' . $request->ip() . ':' . $request->route()->getName() ?? $request->path();
    }

    /**
     * Cria resposta de rate limit excedido
     */
    protected function buildResponse(string $key, int $maxAttempts): Response
    {
        $retryAfter = $this->limiter->availableIn($key);

        return response()->json([
            'error' => 'too_many_requests',
            'message' => 'Muitas requisições. Tente novamente em ' . $retryAfter . ' segundos.',
            'retry_after' => $retryAfter,
        ], 429)->withHeaders([
            'Retry-After' => $retryAfter,
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => 0,
        ]);
    }

    /**
     * Adiciona headers de rate limit na resposta
     */
    protected function addHeaders(Response $response, int $maxAttempts, int $remainingAttempts): Response
    {
        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ]);
    }

    /**
     * Calcula tentativas restantes
     */
    protected function calculateRemainingAttempts(string $key, int $maxAttempts): int
    {
        return $maxAttempts - $this->limiter->attempts($key);
    }
}
