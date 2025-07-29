<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        //fatching the acesstoken
        $user = Socialite::driver('google')->user();
        
        //find or create user
        $authUser = User::firstOrCreate(
            ['email' => $user->getEmail()],
            [
                'name' => $user->getName(),
                'google_id' => $user->getId(),
                'avatar' => $user->getAvatar(),
                //se possivel pensar em adicionar uma senha aleatória
                //pois caso futuramente usemos podemos redefinir a senha
                //'password' => bcrypt(\Illuminate\Support\Str::random(16))

            ]
        );

        //logic in the user
        Auth::login($authUser, true);

        //redirect to home
        return redirect('/');
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
