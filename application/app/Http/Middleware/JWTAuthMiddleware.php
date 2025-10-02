<?php

namespace App\Http\Middleware;

use App\Services\JWTService;
use App\Models\User;
use App\Utils\Tools;
use Closure;
use Illuminate\Http\Request;
use Psr\Log\LogLevel;

class JWTAuthMiddleware
{
    private JWTService $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $this->extractToken($request);

        if (!$token) {
            Tools::logAuthEvent(LogLevel::WARNING, "Authentication failed - Token not provided", [
                'ip_context' => Tools::getIpContext(),
                'route' => $request->route()?->getName() ?? 'unknown',
                'method' => $request->method()
            ]);

            return Tools::error('Token not provided', 401);
        }

        try {
            $payload = $this->jwtService->validateToken($token);

            if ($payload['type'] !== 'access') {
                Tools::logAuthEvent(LogLevel::WARNING, "Authentication failed - Invalid token type", [
                    'token_type' => $payload['type'] ?? 'unknown',
                    'ip_context' => Tools::getIpContext()
                ]);

                return Tools::error('Invalid token type', 401);
            }

            $user = User::find($payload['sub']);
            if (!$user) {
                Tools::logAuthEvent(LogLevel::WARNING, "Authentication failed - User not found", [
                    'user_id' => $payload['sub'] ?? 'unknown',
                    'ip_context' => Tools::getIpContext()
                ]);

                return Tools::error('User not found', 401);
            }

            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            $request->merge(['jwt_payload' => $payload]);

            if (config('app.debug')) {
                Tools::logAuthEvent(LogLevel::DEBUG, "Authentication successful", [
                    'user_context' => Tools::getUserContext($user->id),
                    'token_jti' => $payload['jti'] ?? 'unknown'
                ]);
            }

        } catch (\Exception $e) {
            Tools::logAuthEvent(LogLevel::WARNING, "Authentication failed - Token validation error", [
                'error' => $e->getMessage(),
                'ip_context' => Tools::getIpContext(),
                'route' => $request->route()?->getName() ?? 'unknown'
            ]);

            return Tools::error($e->getMessage(), 401);
        }

        return $next($request);
    }

    private function extractToken(Request $request): ?string
    {
        $cookieToken = $request->cookie('access_token');
        if ($cookieToken) {
            return $cookieToken;
        }

        return null;
    }
}
