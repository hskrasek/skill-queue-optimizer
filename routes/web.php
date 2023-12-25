<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/')
    ->name('home')
    ->uses([App\Http\Controllers\ESIOAuthController::class, 'ssoRedirect']);

Route::get('/auth/callback')
    ->name('auth.callback')
    ->uses([App\Http\Controllers\ESIOAuthController::class, 'oauthCallback']);

Route::get('/app')
    ->name('app')
    ->middleware('auth')
    ->uses([App\Http\Controllers\AppController::class, 'index']);

Route::post('/optimize')
    ->name('optimize')
    ->middleware('auth')
    ->uses([App\Http\Controllers\AppController::class, 'optimize']);
