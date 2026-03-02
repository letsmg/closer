<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    FeedController,
    LocalizacaoController,
    PreferenciaController,
    PagamentoController,
    ShortsController,
    ChatController,
    FotoController,
    PerfilController,
    InteracaoController,
    SegundaChanceController,
    UsuarioController
};


/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::post('/cadastrar', [UsuarioController::class, 'cadastrar'])->name('cadastrar');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Localização
// Rota para buscar cidades com limitação de 120 requisições por minuto para evitar abuso
// o geonames tem um limite de 1000 requisições por hora, então 120 por minuto é um valor seguro para não atingir esse limite
Route::middleware('throttle:120,1')
    ->get('/buscar-cidades', [LocalizacaoController::class, 'buscarCidades']);

/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::get('/quem-me-deu-like', [LikeController::class, 'index'])->middleware('plus');


    Route::put('/usuario/atualizar', [UsuarioController::class, 'atualizar']);
    Route::delete('/usuario/excluir', [UsuarioController::class, 'excluir']);
    /*
    |--------------------------------------------------------------------------
    | Usuário / Perfil
    |--------------------------------------------------------------------------
    */

    Route::get('/user', fn(Request $request) => $request->user());
    Route::get('/perfil', [PerfilController::class, 'show']);
    Route::put('/perfil', [PerfilController::class, 'update']);
    Route::post('/perfil/atualizar-localizacao', [LocalizacaoController::class, 'atualizarLocalizacao']);
    Route::post('/perfil/aceitar-promocao', [PerfilController::class, 'aceitarPromocao']);

    /*
    |--------------------------------------------------------------------------
    | Feed Inteligente
    |--------------------------------------------------------------------------
    */

    Route::get('/feed', [FeedController::class, 'buscarPerfis']);

    /*
    |--------------------------------------------------------------------------
    | Interações
    |--------------------------------------------------------------------------
    */

    Route::post('/like/{perfil}', [InteracaoController::class, 'like']);
    Route::post('/dislike/{perfil}', [InteracaoController::class, 'dislike']);
    Route::post('/segunda-chance/{perfil}', [InteracaoController::class, 'segundaChance']);

    /*
    |--------------------------------------------------------------------------
    | Segundas Chances
    |--------------------------------------------------------------------------
    */

    Route::get('/segundas-chances', [SegundaChanceController::class, 'index']);
    Route::post('/segundas-chances/{id}/usar', [SegundaChanceController::class, 'usar']);

    /*
    |--------------------------------------------------------------------------
    | Preferências
    |--------------------------------------------------------------------------
    */

    Route::get('/preferencias', [PreferenciaController::class, 'index']);
    Route::post('/perfil/preferencias', [PreferenciaController::class, 'sincronizar']);

    /*
    |--------------------------------------------------------------------------
    | Fotos
    |--------------------------------------------------------------------------
    */

    Route::post('/fotos', [FotoController::class, 'upload']);
    Route::delete('/fotos/{id}', [FotoController::class, 'destroy']);
    Route::post('/fotos/reordenar', [FotoController::class, 'reordenar']);

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

    Route::post('/pagamento/verificar', [PagamentoController::class, 'verificarCompra']);

});