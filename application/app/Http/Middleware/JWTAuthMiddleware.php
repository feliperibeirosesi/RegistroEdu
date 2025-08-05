<?php

namespace App\Http\Middleware;

use App\Services\JWTService;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

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
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            $payload = $this->jwtService->validateToken($token);

            if ($payload['type'] !== 'access') {
                return response()->json(['error' => 'Invalid token type'], 401);
            }

            $user = User::find($payload['sub']);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }

            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            $request->merge(['jwt_payload' => $payload]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }

        return $next($request);
    }

    private function extractToken(Request $request): ?string
    {
        $bearerToken = $request->bearerToken();

        if ($bearerToken) {
            return $bearerToken;
        }

        return $request->cookie('access_token');
    }
}
