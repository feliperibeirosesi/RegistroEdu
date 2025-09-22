<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\AccountController;

Route::view('/error', 'pages.error')->name('error');
Route::view('/status', 'pages.status')->name('status');
Route::view('/confirmed', 'pages.email-confirmed')->name('confirmed');

Route::middleware('throttle:5,1')->group(function () {
    Route::get('/auth/verify-email/{token}', [EmailVerificationController::class, 'verifyEmail'])
        ->name('verify.email');
});

Route::middleware(['throttle:10,1', 'jwt.auth', 'user:pending_email_verification,waiting_admin_approval,approved'])->group(function () {
    Route::prefix('account')->group(function () {
        Route::post('/resend-verification', [AccountController::class, 'resendEmailVerification'])
            ->name('account.resend-verification');
        Route::post('/status', [AccountController::class, 'checkAccountStatus'])
            ->name('account.check-status');
        Route::post('/manage', [AccountController::class, 'manageAccount'])
            ->name('account.manage');
    });
});

Route::middleware(['jwt.auth', 'user:approved'])->group(function () {
    Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::post('auth/revoke-all', [AuthController::class, 'revokeAllSessions'])->name('auth.revoke-all');

    Route::middleware(['web', 'proxy.check:log-only'])->group(function () {
        Route::post('auth/refresh', [AuthController::class, 'refresh'])
             ->name('auth.refresh');
    });
});

Route::middleware(['jwt.auth', 'admin'])->prefix('admin')->group(function () {
    Route::post('users/{user}/approve', [UserApprovalController::class, 'approve'])
         ->name('admin.users.approve');

    Route::post('users/{user}/reject', [UserApprovalController::class, 'reject'])
         ->name('admin.users.reject');
});

Route::middleware(['throttle:60,1', 'jwt.auth', 'admin'])->group(function () {
    Route::get('/health', [HealthController::class, 'basic'])->name('health.basic');
    Route::get('/health/detailed', [HealthController::class, 'detailed'])->name('health.detailed');

    Route::get('/health/ping', function () {
        return response()->json([
            'res' => 'pong',
            'timestamp' => now()->toISOString()
        ]);
    });
});

Route::middleware(['web', 'proxy.check:block-proxies,block-vpns,block-high-risk,risk-80'])->group(function () {
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])
         ->name('auth.google.redirect');
});

Route::middleware(['web', 'proxy.check:block-high-risk,risk-75'])->group(function () {
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])
         ->name('auth.google.callback');
});

Route::middleware(['web', 'proxy.check:log-only'])->group(function () {
    Route::get('/{any}', function () {
        return view('react');
    })->where('any', '.*')->name('spa.catchall');
});
