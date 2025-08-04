<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Utils\Tools;
use App\Models\User;

class GoogleController extends Controller
{
    private array $allowedDomains = [
        'professor.educacao.sp.gov.br',
        'educacao.sp.gov.br',
    ];

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = $googleUser->getEmail();

            // if (!$email) {
            //     return Tools::res(
            //         'E-mail não disponível',
            //         400
            //     );
            // }

            // if (!$this->isAllowedDomain($email)) {
            //     return Tools::res(
            //         'Domínio não autorizado',
            //         403
            //     );
            // }

            $user = $this->findOrCreateUser($googleUser);

            Auth::login($user);

            return redirect()->intended('/');
        } catch (\Exception $e) {
            return Tools::res('Erro no login com Google', 500);
        }
    }

    private function isAllowedDomain(string $email): bool
    {
        $domain = substr(strrchr($email, '@'), 1);
        return in_array($domain, $this->allowedDomains, true);
    }

    private function findOrCreateUser($googleUser): User
    {
        return User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => bcrypt(Str::random(16)),
            ]
        );
    }
}
