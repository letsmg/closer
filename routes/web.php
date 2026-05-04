<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Vue SPA
|--------------------------------------------------------------------------
|
| Todas as rotas web são gerenciadas pelo Vue Router (Single Page App).
| O Laravel serve apenas o template inicial.
|
*/

// Catch-all route for Vue SPA
// All routes not matching API or Auth routes go here
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');