<?php

use App\Http\Controllers\Api\JwtAuthController;
use App\Http\Controllers\Web\WebAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ROTAS DE AUTENTICAÇÃO - SISTEMA HÍBRIDO
|--------------------------------------------------------------------------
|
| Este arquivo contém todas as rotas de autenticação, separadas por tipo:
| - API: Para Flutter, Mobile, SPAs (usam JWT)
| - Web: Para aplicações Laravel tradicionais (usam Session)
|
*/

/*
|--------------------------------------------------------------------------
| ROTAS API - JWT (Para Flutter, Mobile, SPAs)
|--------------------------------------------------------------------------
| Todas as rotas abaixo retornam JSON e usam JWT para autenticação.
| Base path: /api/auth
| Headers necessários:
|   - Content-Type: application/json
|   - Accept: application/json
|   - Authorization: Bearer {token} (para rotas protegidas)
*/

Route::prefix('api/auth')->group(function () {
    
    // Rotas públicas
    Route::post('/register', [JwtAuthController::class, 'register'])->name('api.auth.register');
    Route::post('/login', [JwtAuthController::class, 'login'])->name('api.auth.login');
    
    // Rotas protegidas (requerem JWT)
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/logout', [JwtAuthController::class, 'logout'])->name('api.auth.logout');
        Route::post('/refresh', [JwtAuthController::class, 'refresh'])->name('api.auth.refresh');
        Route::get('/me', [JwtAuthController::class, 'me'])->name('api.auth.me');
        Route::post('/revoke-all', [JwtAuthController::class, 'revokeAllTokens'])->name('api.auth.revoke-all');
    });
});

/*
|--------------------------------------------------------------------------
| ROTAS WEB - Session (Para Laravel Web tradicional)
|--------------------------------------------------------------------------
| Todas as rotas abaixo usam sessões PHP e retornam views/redirects.
| Base path: /
*/

Route::middleware(['web'])->group(function () {
    
    // Rotas públicas
    Route::get('/login', [WebAuthController::class, 'showLoginForm'])
        ->name('login')
        ->middleware('guest');
    
    Route::post('/login', [WebAuthController::class, 'login'])
        ->name('login.post')
        ->middleware(['guest', 'throttle:5,1']); // 5 tentativas por minuto
    
    Route::get('/register', [WebAuthController::class, 'showRegisterForm'])
        ->name('register')
        ->middleware('guest');
    
    Route::post('/register', [WebAuthController::class, 'register'])
        ->name('register.post')
        ->middleware(['guest', 'throttle:3,1']); // 3 registros por minuto
    
    // Rotas protegidas (requerem sessão)
    Route::post('/logout', [WebAuthController::class, 'logout'])
        ->name('logout')
        ->middleware('auth');
});

/*
|--------------------------------------------------------------------------
| ROTAS DE VERIFICAÇÃO DE EMAIL
|--------------------------------------------------------------------------
| Suporta tanto web quanto API
*/

// Web - Verificação de email padrão do Laravel
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    
    Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
        // Implementação padrão do Laravel
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    
    Route::post('/email/verification-notification', function () {
        request()->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Link de verificação enviado!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// API - Verificação de email via endpoint
Route::prefix('api')->middleware(['auth:api'])->group(function () {
    Route::get('/email/verify-status', function () {
        return response()->json([
            'verified' => request()->user()->hasVerifiedEmail(),
            'email' => request()->user()->email,
        ]);
    })->name('api.verification.status');
    
    Route::post('/email/resend-verification', function () {
        if (request()->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email já verificado.'], 400);
        }
        
        request()->user()->sendEmailVerificationNotification();
        
        return response()->json(['message' => 'Link de verificação enviado!']);
    })->middleware(['throttle:6,1'])->name('api.verification.resend');
});
