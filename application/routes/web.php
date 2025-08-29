<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleController;

// Rotas de autenticação Google
Route::middleware('web')->group(function () {
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});

// Teste de sessão (precisa vir antes do React catch-all)
Route::get('/teste-session', function () {
    session(['teste' => 'ok']);
    return response()->json(session()->all());
});

// Rota de React (catch-all, sempre por último)
Route::get('/{any}', function () {
    return view('react');
})->where('any', '^(?!auth/google|teste-session).*');
