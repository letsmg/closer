<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware para verificar limites diários de likes e mensagens
 * 
 * FREE: 70 likes/dia, 0 mensagens sem match
 * PLUS: likes ilimitados, 10 mensagens/dia sem match
 * PREMIUM: likes ilimitados, 50 mensagens/dia sem match
 */
class CheckDailyLimits
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $type = 'like')
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $today = now()->toDateString();

        if ($type === 'like') {
            $limit = $user->getDailyMatchesLimit();
            
            // Ilimitado
            if ($limit === PHP_INT_MAX) {
                return $next($request);
            }

            // Verificar se já atingiu o limite
            if ($user->daily_likes_date === $today && $user->daily_likes_count >= $limit) {
                return response()->json([
                    'message' => "Daily like limit reached ({$limit} likes). Upgrade to Plus for unlimited likes.",
                    'limit' => $limit,
                    'used' => $user->daily_likes_count,
                    'reset_at' => now()->endOfDay()->toISOString(),
                ], 429);
            }

            // Resetar contagem se for um novo dia
            if ($user->daily_likes_date !== $today) {
                $user->daily_likes_count = 0;
                $user->daily_likes_date = $today;
                $user->save();
            }
        }

        if ($type === 'message') {
            // FREE não pode enviar mensagens sem match
            if ($user->isFree()) {
                return response()->json([
                    'message' => 'Free users cannot send messages without an active match. Upgrade to Plus to send messages.',
                ], 403);
            }

            $limit = $user->getDailyMessagesLimit();

            // Ilimitado
            if ($limit === PHP_INT_MAX) {
                return $next($request);
            }

            // Verificar se já atingiu o limite
            if ($user->daily_messages_date === $today && $user->daily_messages_count >= $limit) {
                return response()->json([
                    'message' => "Daily message limit reached ({$limit} messages without match). Upgrade to Premium for more.",
                    'limit' => $limit,
                    'used' => $user->daily_messages_count,
                    'reset_at' => now()->endOfDay()->toISOString(),
                ], 429);
            }

            // Resetar contagem se for um novo dia
            if ($user->daily_messages_date !== $today) {
                $user->daily_messages_count = 0;
                $user->daily_messages_date = $today;
                $user->save();
            }
        }

        return $next($request);
    }
}