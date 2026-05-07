<?php

use App\Http\Controllers\SpaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Vue SPA
|--------------------------------------------------------------------------
|
| All web routes are managed by Vue Router (Single Page App).
| Laravel serves only the initial template.
|
*/

// Terms of Service route
Route::get('/terms', [SpaController::class, 'terms'])->name('terms');

// Privacy Policy route
Route::get('/privacy', [SpaController::class, 'privacy'])->name('privacy');

// Security Policy route
Route::get('/security', [SpaController::class, 'security'])->name('security');

// Catch-all route for Vue SPA
// All routes not matching API or Auth routes go here
Route::get('/{any?}', [SpaController::class, 'index'])->where('any', '.*')->name('login');