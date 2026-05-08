<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        // Aplica trim e strip_tags recursivamente em todos os campos de texto
        array_walk_recursive($input, function (&$item) {
            if (is_string($item)) {
                $item = strip_tags(trim($item));
            }
        });

        // Substitui o input original pelo sanitizado
        $request->merge($input);

        return $next($request);
    }
}
