<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => bcrypt(Str::random(16))
                ]
            );

            Auth::login($user);

            return redirect()->intended('/');

        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Something went wrong.');
        }
    }
}
