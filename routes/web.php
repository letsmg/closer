<?php

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
Route::get('/terms', function () {
    return view('terms-popup');
})->name('terms');

// Privacy Policy route
Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

// Security Policy route
Route::get('/security', function () {
    return view('security');
})->name('security');

// Catch-all route for Vue SPA
// All routes not matching API or Auth routes go here
Route::middleware(['terms.accepted'])->get('/{any?}', function () {
    return view('app');
})->where('any', '.*')->name('spa');