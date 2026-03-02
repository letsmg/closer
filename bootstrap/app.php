<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RegistrarAcesso;
use App\Http\Middleware\UpdateUserOnlineStatus; // O novo middleware

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
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

        // Adicione o apelido aqui embaixo
        $middleware->alias([
            'plus' => \App\Http\Middleware\VerificarAcessoPlus::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();