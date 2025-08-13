<?php

//define que essa classe está com o namespace
namespace App\Http\Controllers;

//importa classes e as facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Utils\Tools;
use App\Models\User;

//lista dos emails permitidos para o login
class GoogleController extends Controller
{
    private array $allowedDomains = [
        'professor.educacao.sp.gov.br',
        'educacao.sp.gov.br',
    ];

    //redireciona a pessoa para a página de login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {  //obtem dados do usuarios vindos do google
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = $googleUser->getEmail();

            //caso não retorne o email, ele retorna uma imagem de erro
            if (!$email) {
                return Tools::res(
                    'E-mail não disponível',
                    400
                );
            }

            // Verifica se o domínio do e-mail está na lista de permitidos
            if (!$this->isAllowedDomain($email)) {
                return Tools::res(
                    'Domínio não autorizado',
                    403
                );
            }

            // Procura o usuário no banco ou ele irá criar um caso não exista
            $user = $this->findOrCreateUser($googleUser);

            // Autentica o usuário no sistema
            Auth::login($user);

            // Redireciona para a página que o usuário estava tentando acessar
            return redirect()->intended('/');
        } catch (\Exception $e) {
            // Caso tenha algum erro no processo, retorna mensagem de erro
            return Tools::res('Erro no login com Google:' . $e, 500);
        }
    }

    private function isAllowedDomain(string $email): bool
    {
        // Extrai o domínio do e-mail (depois do @)
        $domain = substr(strrchr($email, '@'), 1);
        // Retorna true se o domínio estiver na lista de permitidos
        return in_array($domain, $this->allowedDomains, true);
    }

    private function findOrCreateUser($googleUser): User
    {
        // Busca usuário pelo e-mail, se não existir, cria um novo com os dados do Google
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
