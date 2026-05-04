<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RegistrarAcesso;
use App\Http\Middleware\UpdateUserOnlineStatus;
use App\Http\Middleware\HybridAuth;
use App\Http\Middleware\HybridVerified;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
            __DIR__.'/../routes/auth.php',
        ],
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // Adiciona os middlewares para as rotas da API
        $middleware->api(append: [
            RegistrarAcesso::class,
            UpdateUserOnlineStatus::class, // Adicionado aqui
        ]);

        // Aliases dos middlewares
        $middleware->alias([
            'plus' => \App\Http\Middleware\VerificarAcessoPlus::class,
            'auth.hybrid' => HybridAuth::class,
            'verified.hybrid' => HybridVerified::class,
            'scope' => \App\Http\Middleware\OAuthScope::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();