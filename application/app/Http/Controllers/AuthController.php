<?php

namespace App\Http\Controllers;

use App\Services\JWTService;
use App\Services\ProxyCheckService;
use App\Utils\Tools;
use Illuminate\Http\Request;
use Psr\Log\LogLevel;

class AuthController extends Controller
{
    private JWTService $jwtService;

    private ProxyCheckService $proxyCheck;

    public function __construct(JWTService $jwtService, ProxyCheckService $proxyCheck)
    {
        $this->jwtService = $jwtService;
        $this->proxyCheck = $proxyCheck;
    }

    public function refresh(Request $request)
    {
        $refreshToken = $request->input('refresh_token') ?: $request->cookie('refresh_token');

        if (! $refreshToken) {
            Tools::logAuthEvent(LogLevel::WARNING, 'Refresh token not provided',
                Tools::getIpContext()
            );

            return Tools::error('Refresh token não fornecido', 400);
        }

        try {
            $tokenData = $this->jwtService->refreshAccessToken($refreshToken);

            Tools::logAuthEvent(LogLevel::INFO, 'Token refreshed successfully',
                Tools::getUserContext($tokenData['user']->id)
            );

            return Tools::tokenResponse('Token renovado com sucesso', $tokenData);

        } catch (\Exception $e) {
            Tools::logAuthEvent(LogLevel::WARNING, 'Token refresh failed', [
                'error' => $e->getMessage(),
                'ip_context' => Tools::getIpContext(),
            ]);

            return Tools::error($e->getMessage(), 401);
        }
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $payload = $request->get('jwt_payload', []);

        Tools::logAuthEvent(LogLevel::DEBUG, 'User profile accessed',
            Tools::getUserContext($user->id)
        );

        return Tools::success('Dados do usuário', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'google_id' => $user->google_id,
                'provider' => $user->provider,
                'last_login_at' => $user->last_login_at,
                'last_login_ip' => $user->last_login_ip,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'is_admin' => $user->isAdmin(),
                'is_user' => $user->isUser(),
                'active_sessions_count' => $user->getActiveSessionsCount(),
            ],
            'token_info' => [
                'issued_at' => $payload['iat'] ?? null,
                'expires_at' => $payload['exp'] ?? null,
                'jti' => $payload['jti'] ?? null,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $refreshToken = $request->input('refresh_token') ?: $request->cookie('refresh_token');

        try {
            if ($refreshToken) {
                $this->jwtService->revokeRefreshToken($refreshToken);
            }

            Tools::logAuthEvent(LogLevel::INFO, 'User logged out',
                Tools::getUserContext($user->id)
            );

            return Tools::success('Logout realizado com sucesso')
                ->cookie('access_token', '', -1)
                ->cookie('refresh_token', '', -1);

        } catch (\Exception $e) {
            Tools::logAuthEvent(LogLevel::ERROR, 'Logout error', [
                'error' => $e->getMessage(),
                'user_context' => Tools::getUserContext($user->id),
            ]);

            return Tools::error('Erro no logout', 500);
        }
    }

    public function revokeAllSessions(Request $request)
    {
        $user = $request->user();

        try {
            $this->jwtService->revokeAllUserTokens($user->id);

            Tools::logAuthEvent(LogLevel::WARNING, 'All user sessions revoked',
                Tools::getUserContext($user->id)
            );

            return Tools::success('Todas as sessões foram revogadas')
                ->cookie('access_token', '', -1)
                ->cookie('refresh_token', '', -1);

        } catch (\Exception $e) {
            Tools::logAuthEvent(LogLevel::ERROR, 'Failed to revoke all sessions', [
                'error' => $e->getMessage(),
                'user_context' => Tools::getUserContext($user->id),
            ]);

            return Tools::error('Erro ao revogar sessões', 500);
        }
    }

    private function generateTokenResponse(\App\Models\User $user, Request $request)
    {
        $accessToken = $this->jwtService->generateAccessToken($user);
        $refreshToken = $this->jwtService->generateRefreshToken($user);

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => Tools::getRealIp(),
            'ip_info' => $request->get('security_info.ip_info', []),
        ]);

        $tokenData = [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.access_ttl', 60) * 60,
            'user' => $user,
        ];

        Tools::logAuthEvent(LogLevel::INFO, 'Token generated for user',
            Tools::getUserContext($user->id)
        );

        return Tools::tokenResponse('Login realizado com sucesso', $tokenData);
    }

    private function validateLoginSecurity(Request $request, string $email): bool
    {
        $ip = Tools::getRealIp();
        $ipInfo = $request->get('security_info.ip_info', $this->proxyCheck->checkIp($ip));

        $securityConfig = [
            'block_proxies' => config('security.login.block_proxies', false),
            'block_vpns' => config('security.login.block_vpns', false),
            'block_high_risk' => config('security.login.block_high_risk', true),
            'risk_threshold' => config('security.login.risk_threshold', 85),
        ];

        $shouldBlock = false;
        $reasons = [];

        if ($securityConfig['block_proxies'] && ($ipInfo['is_proxy'] ?? false)) {
            $shouldBlock = true;
            $reasons[] = 'proxy_detected';
        }

        if ($securityConfig['block_vpns'] && ($ipInfo['is_vpn'] ?? false)) {
            $shouldBlock = true;
            $reasons[] = 'vpn_detected';
        }

        if ($securityConfig['block_high_risk'] &&
            ($ipInfo['risk_score'] ?? 0) >= $securityConfig['risk_threshold']) {
            $shouldBlock = true;
            $reasons[] = 'high_risk_score';
        }

        if ($shouldBlock) {
            Tools::logSecurityEvent(LogLevel::WARNING, 'Login blocked by security policy', [
                'email' => $email,
                'block_reasons' => $reasons,
                'ip_context' => Tools::getIpContext($ipInfo),
                'security_config' => $securityConfig,
            ]);
        }

        return ! $shouldBlock;
    }
}
