<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('page.HomeScreen');
});

Route::get('/singin', function () {
    return view('page.RegisterScreen');
});
