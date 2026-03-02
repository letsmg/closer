<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserOnlineStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Usamos 'last_seen_at' para seguir o padrão Laravel (Timestamps)
            // Se não houve atividade nos últimos 3 a 5 minutos, atualizamos.
            if (!$user->last_seen_at || $user->last_seen_at->diffInMinutes(now()) >= 3) {
                
                // Usamos updateQuietly para não disparar observers de "User Updated" 
                // desnecessariamente (como enviar e-mails de alteração de conta)
                $user->updateQuietly([
                    'last_seen_at' => now()
                ]);
            }
        }

        return $next($request);
    }
}