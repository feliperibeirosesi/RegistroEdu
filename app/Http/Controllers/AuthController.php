<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        // Handle the callback from Google after authentication
        // You can retrieve the user information and log them in or register them
        // For now, just return a success message
        return response()->json(['message' => 'Google authentication successful!']);
    }

    public function logout()
    {
        // Handle the logout logic
        // For now, just return a success message
        return response()->json(['message' => 'Logged out successfully!']);
    }
    public function index(){
        return view("page.HomeScreen");
    }
    public function register(){
        return view("page.RegisterScreen");
    }
}
