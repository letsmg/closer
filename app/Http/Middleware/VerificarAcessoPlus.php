<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarAcessoPlus
{
    /**
     * Handle an incoming request.
     *
     * Verifica se o usuário tem permissão para acessar o recurso.
     * Por padrão, verifica hasPlusAccess().
     * Se o parâmetro 'feature' for passado, verifica uma feature específica.
     *
     * Exemplos de uso nas rotas:
     *   ->middleware('plus')                          // hasPlusAccess()
     *   ->middleware('plus:viewLikes')                // canViewLikes()
     *   ->middleware('plus:sendMessagesWithoutMatch')  // canSendMessagesWithoutMatch()
     */
    public function handle(Request $request, Closure $next, ?string $feature = null): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(403, 'Acesso negado.');
        }

        // Se uma feature específica foi solicitada, verifica o método correspondente
        if ($feature) {
            $methodName = 'can' . ucfirst($feature);
            
            if (method_exists($user, $methodName)) {
                if (!$user->{$methodName}()) {
                    abort(403, 'Acesso negado. Recurso não disponível para seu plano.');
                }
                return $next($request);
            }
        }

        // Comportamento padrão: verifica hasPlusAccess()
        if (!$user->hasPlusAccess()) {
            abort(403, 'Acesso negado.');
        }
        
        return $next($request);
    }
}
