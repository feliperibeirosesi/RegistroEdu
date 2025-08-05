<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Utils\Tools;
use App\Models\User;
use App\Services\JWTService;
use App\Services\ProxyCheckService;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    private array $allowedDomains = [
        'professor.educacao.sp.gov.br',
        'educacao.sp.gov.br',
    ];

    private JWTService $jwtService;
    private ProxyCheckService $proxyCheck;

    public function __construct(JWTService $jwtService, ProxyCheckService $proxyCheck)
    {
        $this->jwtService = $jwtService;
        $this->proxyCheck = $proxyCheck;
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = $googleUser->getEmail();

            $ip = $request->get('real_ip', $request->ip());
            $ipInfo = $request->get('ip_info', $this->proxyCheck->checkIp($ip));

            Log::info('Google OAuth attempt', [
                'email' => $email,
                'ip' => $ip,
                'country' => $ipInfo['country'] ?? 'Unknown',
                'risk_score' => $ipInfo['risk_score'] ?? 0,
                'is_proxy' => $ipInfo['is_proxy'] ?? false,
                'is_vpn' => $ipInfo['is_vpn'] ?? false
            ]);

            if ($this->shouldBlockOAuthLogin($ipInfo, $email)) {
                Log::warning('OAuth login blocked', [
                    'email' => $email,
                    'ip' => $ip,
                    'reason' => 'High risk IP or suspicious activity'
                ]);

                return redirect(config('app.frontend_url', '/') . '/login?error=security_block');
            }

            // if (!$email) {
            //     return Tools::res('E-mail não disponível', 400);
            // }

            // if (!$this->isAllowedDomain($email)) {
            //     return Tools::res('Domínio não autorizado', 403);
            // }

            $user = $this->findOrCreateUser($googleUser, $ip, $ipInfo);

            $accessToken = $this->jwtService->generateAccessToken($user);
            $refreshToken = $this->jwtService->generateRefreshToken($user);

            Log::info('Google OAuth successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $ip,
                'country' => $ipInfo['country'] ?? 'Unknown'
            ]);

            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $ip,
                'ip_info' => $ipInfo
            ]);

            if ($request->expectsJson() || $request->wantsJson()) {
                return Tools::res('Login realizado com sucesso', 200, [
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'token_type' => 'Bearer',
                    'expires_in' => config('jwt.access_ttl', 60) * 60,
                    'user' => $user
                ]);
            }

            $frontendUrl = config('app.frontend_url', '/');
            return redirect($frontendUrl . '/auth/callback?token=' . $accessToken)
                ->cookie('access_token', $accessToken, config('jwt.access_ttl', 60))
                ->cookie('refresh_token', $refreshToken, config('jwt.refresh_ttl', 20160), null, null, true, true);

        } catch (\Exception $e) {
            Log::error('Google OAuth error', [
                'error' => $e->getMessage(),
                'ip' => $request->get('real_ip', $request->ip()),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return Tools::res('Erro no login com Google', 500);
            }

            return redirect(config('app.frontend_url', '/') . '/login?error=oauth_failed');
        }
    }

    private function isAllowedDomain(string $email): bool
    {
        $domain = substr(strrchr($email, "@"), 1);
        return in_array($domain, $this->allowedDomains);
    }

    private function shouldBlockOAuthLogin(array $ipInfo, string $email): bool
    {
        return (
            ($ipInfo['is_proxy'] ?? false) ||
            ($ipInfo['is_vpn'] ?? false) ||
            ! $this->isAllowedDomain($email)
        );
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
                'ip_info' => $ipInfo
            ]);
        }

        return $user;
    }
}

