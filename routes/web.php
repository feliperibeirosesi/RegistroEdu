<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/', [AuthController::class, 'index']);

Route::get('/singin', [AuthController::class, 'register']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/test-session', function () {
    session(['foo' => 'bar']);
    return session('foo');
});


