<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [HealthController::class, 'basic'])->name('health.basic');
Route::get('/health/detailed', [HealthController::class, 'detailed'])->name('health.detailed');

Route::middleware(['web', 'proxy.check:block-proxies,block-vpns,block-high-risk,risk-80'])->group(function () {
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])
        ->name('auth.google.redirect');
});

Route::middleware(['web', 'proxy.check:block-high-risk,risk-75'])->group(function () {
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');
});

Route::middleware(['web', 'proxy.check:log-only'])->group(function () {
    Route::post('auth/refresh', [AuthController::class, 'refresh'])
        ->name('auth.refresh');
});

Route::middleware(['jwt.auth'])->group(function () {
    Route::get('auth/me', [AuthController::class, 'me'])
        ->name('auth.me');

    Route::post('auth/logout', [AuthController::class, 'logout'])
        ->name('auth.logout');

    Route::post('auth/revoke-all', [AuthController::class, 'revokeAllSessions'])
        ->name('auth.revoke-all');
});

Route::middleware(['throttle:60,1'])->get('health', function () {
    return response()->json([
        'res' => 'pong',
        'timestamp' => now()->toISOString(),
    ]);
});

Route::middleware(['web', 'proxy.check:log-only'])->group(function () {
    Route::get('/{any}', function () {
        return view('react');
    })->where('any', '.*')->name('spa.catchall');
});
