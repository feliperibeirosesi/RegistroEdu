<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Utils\Tools;
use App\Models\User;
use App\Services\JWTService;
use App\Services\ProxyCheckService;
use Illuminate\Support\Str;
use Psr\Log\LogLevel;

class GoogleController extends Controller
{
    private array $allowedDomains = [];
    private JWTService $jwtService;
    private ProxyCheckService $proxyCheck;

    public function __construct(JWTService $jwtService, ProxyCheckService $proxyCheck)
    {
        $this->jwtService = $jwtService;
        $this->proxyCheck = $proxyCheck;

        $this->allowedDomains = app()->environment('local', 'testing')
            ? ['gmail.com']
            : ['professor.educacao.sp.gov.br', 'educacao.sp.gov.br'];
    }

    public function redirectToGoogle()
    {
        Tools::logAuthEvent(LogLevel::DEBUG, "Google OAuth redirect initiated", [
            'redirect_url' => config('services.google.redirect')
        ]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = $googleUser->getEmail();
            $ip = Tools::getRealIp();
            $ipInfo = $request->get('security_info.ip_info', $this->proxyCheck->checkIp($ip));

<<<<<<< HEAD
            if (!$email) {
                return Tools::res(
                    'E-mail não disponível',
                    400
                );
            }

            if (!$this->isAllowedDomain($email)) {
                return Tools::res(
                    'Domínio não autorizado',
                    403
                );
            }
=======
            Tools::logAuthEvent(LogLevel::INFO, "Google OAuth callback received", [
                'email' => $email,
                'domain' => $this->extractDomain($email),
                'ip_context' => Tools::getIpContext($ipInfo),
                'environment' => app()->environment()
            ]);

            if ($this->shouldBlockOAuthLogin($ipInfo, $email)) {
                Tools::logLoginAttempt(false, $email, $ipInfo, 'Security policy violation');
>>>>>>> JoaoPaulo

                return $this->handleBlockedLogin($request, $email, $ip, $ipInfo);
            }

            $user = $this->findOrCreateUser($googleUser, $ip, $ipInfo);

            $tokenData = [
                'access_token' => $this->jwtService->generateAccessToken($user),
                'refresh_token' => $this->jwtService->generateRefreshToken($user),
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.access_ttl', 60) * 60,
                'user' => $user
            ];

            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $ip,
                'ip_info' => $ipInfo
            ]);

            Tools::logLoginAttempt(true, $email, $ipInfo);

            if ($request->expectsJson() || $request->wantsJson()) {
                return Tools::tokenResponse('Login realizado com sucesso', $tokenData);
            }

            return $this->handleWebRedirect($tokenData);

        } catch (\Exception $e) {
<<<<<<< HEAD
            return Tools::res('Erro no login com Google:' . $e, 500);
=======
            Tools::logAuthEvent(LogLevel::ERROR, "Google OAuth error", [
                'error' => $e->getMessage(),
                'ip_context' => Tools::getIpContext(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->handleError($request, $e);
>>>>>>> JoaoPaulo
        }
    }

    private function extractDomain(string $email): string
    {
        return substr(strrchr($email, "@"), 1);
    }

    private function isAllowedDomain(string $email): bool
    {
        return in_array($this->extractDomain($email), $this->allowedDomains);
    }

    private function shouldBlockOAuthLogin(array $ipInfo, string $email): bool
    {
        $reasons = [];

        if ($ipInfo['is_proxy'] ?? false) {
            $reasons[] = 'proxy_detected';
        }

        if ($ipInfo['is_vpn'] ?? false) {
            $reasons[] = 'vpn_detected';
        }

        if (!$this->isAllowedDomain($email)) {
            $reasons[] = 'domain_not_allowed';
        }

        if (!empty($reasons)) {
            Tools::logSecurityEvent(LogLevel::WARNING, "OAuth login blocked", [
                'email' => $email,
                'block_reasons' => $reasons,
                'ip_context' => Tools::getIpContext($ipInfo)
            ]);

            return true;
        }

        return false;
    }

    private function handleBlockedLogin(Request $request, string $email, string $ip, array $ipInfo)
    {
        $reasons = [];

        if ($ipInfo['is_proxy'] ?? false) {
            $reasons[] = 'Proxy server detected';
        }

        if ($ipInfo['is_vpn'] ?? false) {
            $reasons[] = 'VPN service detected';
        }

        if (!$this->isAllowedDomain($email)) {
            $reasons[] = 'Email domain not authorized';
        }

        if ($request->expectsJson()) {
            return Tools::securityBlockResponse($reasons, $ipInfo);
        }

        return redirect(config('app.frontend_url', '/') . '/login?error=blocked');
    }

    private function findOrCreateUser($googleUser, string $ip, array $ipInfo): User
    {
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'avatar' => $googleUser->getAvatar(),
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                'last_login_at' => now(),
                'last_login_ip' => $ip,
                'ip_info' => $ipInfo,
                'password' => bcrypt(Str::random(32))
            ]);

            Tools::logAuthEvent(LogLevel::INFO, "New user created via Google OAuth",
                Tools::getUserContext($user->id)
            );
        }

        return $user;
    }

    private function handleWebRedirect(array $tokenData)
    {
        return redirect('http://localhost:8000/singin')
            ->cookie('access_token', $tokenData['access_token'], config('jwt.access_ttl', 60))
            ->cookie('refresh_token', $tokenData['refresh_token'], config('jwt.refresh_ttl', 20160), null, null, true, true);
    }

    private function handleError(Request $request, \Exception $e)
    {
        if ($request->expectsJson()) {
            return Tools::error('Erro no login com Google', 500, [
                'code' => 'OAUTH_ERROR'
            ]);
        }

        return redirect(config('app.frontend_url', '/') . '/login?error=oauth');
    }
}
