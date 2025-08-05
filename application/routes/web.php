<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\AuthController;

Route::middleware(['web', 'proxy.check:block-high-risk,risk-85'])->group(function () {
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
});

Route::middleware(['web', 'proxy.check:block-high-risk,risk-75'])->group(function () {
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});

Route::middleware(['web', 'proxy.check:log-only'])->group(function () {
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
});

Route::middleware(['jwt.auth'])->group(function () {
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
});

Route::get('/{any}', function () {
    return view('react');
})->where('any', '.*');

