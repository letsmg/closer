<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    JwtAuthController,
    OAuth2Controller,
    TwoFactorController,
    FeedController,
    LocationController,
    PreferenceController,
    PaymentController,
    ShortsController,
    ChatController,
    ProfileController,
    InteractionController,
    UserController,
    LikeController,
    BlockController,
    ReportController,
    DiscoveryController
};


/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

// Rotas de autenticação movidas para routes/auth.php
// Mantém apenas a rota legada de cadastro para compatibilidade temporária
Route::post('/cadastrar', [UserController::class, 'cadastrar'])->name('cadastrar.legacy');

/*
|--------------------------------------------------------------------------
| OAUTH2 ENDPOINTS (RFC 6749 Standard)
|--------------------------------------------------------------------------
| Token endpoint com suporte a:
| - password grant (login direto)
| - refresh_token grant (renovação)
| - Escopos OAuth2 (read:profile, write:messages, etc.)
| - Short-lived access tokens (15 min) + Long-lived refresh tokens (30 dias)
|
| Escopos disponíveis:
| read:profile, write:profile, read:feed, write:interactions,
| read:messages, write:messages, read:matches, write:photos,
| read:shorts, write:premium, admin:users
*/

Route::post('/oauth/token', [OAuth2Controller::class, 'token'])->name('oauth.token');
Route::post('/oauth/revoke', [OAuth2Controller::class, 'revoke'])->name('oauth.revoke');
Route::post('/oauth/introspect', [OAuth2Controller::class, 'introspect'])->name('oauth.introspect');

// Endpoint para listar escopos disponíveis (documentação)
Route::get('/oauth/scopes', function () {
    return response()->json([
        'scopes' => \App\Http\Controllers\Api\OAuth2Controller::SCOPES,
    ]);
})->name('oauth.scopes');

// Localização
// Rota para buscar cidades com limitação de 120 requisições por minuto para evitar abuso
// o geonames tem um limite de 1000 requisições por hora, então 120 por minuto é um valor seguro para não atingir esse limite
Route::middleware('throttle:120,1')
    ->get('/buscar-cidades', [LocationController::class, 'buscarCidades']);

/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS - JWT AUTH
|--------------------------------------------------------------------------
|
| Todas as rotas abaixo requerem autenticação JWT válida.
| Header necessário: Authorization: Bearer {seu_token_jwt}
| 
| Compatibilidade:
| - Flutter/Mobile: Use JWT (Bearer token)
| - Web SPA: Use JWT (Bearer token)
| - Web tradicional: Pode usar sessão (hybrid auth)
|
*/

Route::middleware(['auth.hybrid', 'verified.hybrid'])->group(function () {

    Route::get('/quem-me-deu-like', [LikeController::class, 'index'])->middleware('plus');


    Route::put('/usuario/atualizar', [UserController::class, 'atualizar']);
    Route::delete('/usuario/excluir', [UserController::class, 'excluir']);
    /*
    |--------------------------------------------------------------------------
    | Usuário / Perfil
    |--------------------------------------------------------------------------
    */

    Route::get('/user', fn(Request $request) => $request->user());
    Route::get('/perfil', [ProfileController::class, 'show']);
    Route::put('/perfil', [ProfileController::class, 'update']);
    Route::post('/perfil/atualizar-localizacao', [LocationController::class, 'atualizarLocalizacao']);
    // Route::post('/perfil/aceitar-promocao', [PerfilController::class, 'aceitarPromocao']);

    /*
    |--------------------------------------------------------------------------
    | Feed Inteligente
    |--------------------------------------------------------------------------
    */

    Route::get('/feed', [FeedController::class, 'buscarPerfis']);

    /*
    |--------------------------------------------------------------------------
    | Discovery (Swipe Tinder-like)
    |--------------------------------------------------------------------------
    */

    Route::get('/discover', [DiscoveryController::class, 'discover']);
    Route::post('/discover/{profile}/like', [DiscoveryController::class, 'like'])->middleware('limits:like');
    Route::post('/discover/{profile}/dislike', [DiscoveryController::class, 'dislike']);

    /*
    |--------------------------------------------------------------------------
    | Interações
    |--------------------------------------------------------------------------
    */

    Route::post('/like/{perfil}', [InteractionController::class, 'like']);
    Route::post('/dislike/{perfil}', [InteractionController::class, 'dislike']);
    Route::post('/segunda-chance/{perfil}', [InteractionController::class, 'segundaChance']);

    /*
    |--------------------------------------------------------------------------
    | Segundas Chances
    |--------------------------------------------------------------------------
    */

    // Route::get('/segundas-chances', [SegundaChanceController::class, 'index']);
    // Route::post('/segundas-chances/{id}/usar', [SegundaChanceController::class, 'usar']);

    /*
    |--------------------------------------------------------------------------
    | Preferências
    |--------------------------------------------------------------------------
    */

    Route::get('/preferencias', [PreferenceController::class, 'index']);
    Route::post('/perfil/preferencias', [PreferenceController::class, 'sincronizar']);

    /*
    |--------------------------------------------------------------------------
    | Fotos
    |--------------------------------------------------------------------------
    */

    // Route::post('/fotos', [FotoController::class, 'upload']);
    // Route::delete('/fotos/{id}', [FotoController::class, 'destroy']);
    // Route::post('/fotos/reordenar', [FotoController::class, 'reordenar']);

    /*
    |--------------------------------------------------------------------------
    | Shorts
    |--------------------------------------------------------------------------
    */

    Route::get('/shorts/biblioteca', [ShortsController::class, 'index']);
    Route::get('/shorts/meus', [ShortsController::class, 'listarMeusShorts']);
    Route::post('/shorts', [ShortsController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | Chat
    |--------------------------------------------------------------------------
    */

    Route::get('/chat/{matchId}', [ChatController::class, 'show']);
    Route::post('/chat/{matchId}/enviar', [ChatController::class, 'enviarMensagem']);

    /*
    |--------------------------------------------------------------------------
    | Pagamentos
    |--------------------------------------------------------------------------
    */

    Route::post('/pagamento/verificar', [PaymentController::class, 'verificarCompra']);

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication (2FA)
    |--------------------------------------------------------------------------
    */

    Route::get('/2fa/status', [TwoFactorController::class, 'status']);
    Route::get('/2fa/setup', [TwoFactorController::class, 'setup']);
    Route::post('/2fa/confirm', [TwoFactorController::class, 'confirm']);
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable']);
    Route::post('/2fa/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes']);

});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth.hybrid', 'level:staff'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    // Denúncias (Reports)
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/{report}', [ReportController::class, 'show']);
    Route::put('/reports/{report}', [ReportController::class, 'update']);
});