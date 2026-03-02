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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //esse middleware é para verificar se o usuário tem acesso ao conteúdo Plus, ou seja, se ele é premium ou tem nível de acesso 1 ou superior
        $user = auth()->user();
        if (!$user || $user->nivel_acesso < 1) {
            abort(403, 'Acesso negado.');
        }
        
        return $next($request);
    }
}
