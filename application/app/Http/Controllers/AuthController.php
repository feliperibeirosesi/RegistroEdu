<?php

namespace App\Http\Controllers;

use App\Services\JWTService;
use App\Services\ProxyCheckService;
use App\Utils\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

        if (!$refreshToken) {
            return Tools::res('Refresh token não fornecido', 400);
        }

        try {
            $tokenData = $this->jwtService->refreshAccessToken($refreshToken);
            return Tools::res('Token renovado com sucesso', 200, $tokenData);
        } catch (\Exception $e) {
            return Tools::res($e->getMessage(), 401);
        }
    }

    public function logout(Request $request)
    {
        $refreshToken = $request->input('refresh_token') ?: $request->cookie('refresh_token');

        if ($refreshToken) {
            $this->jwtService->revokeRefreshToken($refreshToken);
        }

        $user = $request->user();
        if ($user) {
            Log::info('User logout', [
                'user_id' => $user->id,
                'ip' => $request->get('real_ip', $request->ip())
            ]);
        }

        return Tools::res('Logout realizado com sucesso')
            ->withoutCookie('access_token')
            ->withoutCookie('refresh_token');
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $payload = $request->get('jwt_payload', []);

        return Tools::res('Dados do usuário', 200, [
            'user' => $user,
            'token_info' => [
                'issued_at' => $payload['iat'] ?? null,
                'expires_at' => $payload['exp'] ?? null,
                'jti' => $payload['jti'] ?? null
            ]
        ]);
    }

    private function generateTokenResponse(\App\Models\User $user, Request $request)
    {
        $accessToken = $this->jwtService->generateAccessToken($user);
        $refreshToken = $this->jwtService->generateRefreshToken($user);

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->get('real_ip', $request->ip()),
            'ip_info' => $request->get('ip_info', [])
        ]);

        $tokenData = [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.access_ttl', 60) * 60,
            'user' => $user
        ];

        return Tools::res('Login realizado com sucesso', 200, $tokenData)
            ->cookie('access_token', $accessToken, config('jwt.access_ttl', 60))
            ->cookie('refresh_token', $refreshToken, config('jwt.refresh_ttl', 20160), null, null, true, true);
    }
}
