<?php

namespace App\Http\Middleware;

use App\Enums\UserLevel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para verificar nível de acesso do usuário
 * 
 * Uso: Route::middleware(['auth:api', 'level:premium']) // Premium ou superior
 *      Route::middleware(['auth:api', 'level:admin']) // Admin apenas
 *      Route::middleware(['auth:api', 'level:plus']) // Plus ou superior
 */
class CheckUserLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$levels  Níveis permitidos (separados por vírgula na definição da rota)
     */
    public function handle(Request $request, Closure $next, string ...$levels): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'unauthorized',
                'error_description' => 'Usuário não autenticado.'
            ], 401);
        }

        $userLevel = $user->getLevelAttribute();
        
        // Verifica se o nível do usuário está nos permitidos
        $hasAccess = false;
        
        foreach ($levels as $requiredLevel) {
            $requiredEnum = UserLevel::fromString($requiredLevel);
            
            if ($requiredLevel === 'staff') {
                if ($userLevel->value >= UserLevel::ADMIN->value) {
                    $hasAccess = true;
                    break;
                }
            } elseif ($requiredEnum && $userLevel->value >= $requiredEnum->value) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            return response()->json([
                'error' => 'forbidden',
                'error_description' => 'Nível de acesso insuficiente.',
                'required_level' => implode(' ou ', $levels),
                'current_level' => $userLevel->getName(),
            ], 403);
        }

        // Adiciona informações do nível ao request para uso posterior
        $request->attributes->set('user_level', $userLevel);
        $request->attributes->set('user_level_name', $userLevel->getName());

        return $next($request);
    }
}
