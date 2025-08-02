<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleController;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    if(Auth::check()){
        return view('page.HomeScreen');
    }
    return view('page.HomeScreen');
});



Route::get('/singin', function(){
    return view('page.RegisterScreen');
});


Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/test-session', function () {
    session(['foo' => 'bar']);
    return session('foo');
});


