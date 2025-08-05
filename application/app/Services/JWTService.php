<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class JWTService
{
    private string $secretKey;
    private string $algorithm;
    private int $accessTokenTTL;
    private int $refreshTokenTTL;

    public function __construct()
    {
        $this->secretKey = config('jwt.secret', env('JWT_SECRET'));
        $this->algorithm = config('jwt.algorithm', 'HS256');
        $this->accessTokenTTL = config('jwt.access_ttl', 60);
        $this->refreshTokenTTL = config('jwt.refresh_ttl', 20160);
    }

    public function generateAccessToken(User $user): string
    {
        $payload = [
            'iss' => config('app.name'),
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + ($this->accessTokenTTL * 60),
            'jti' => uniqid(),
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'type' => 'access'
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function generateRefreshToken(User $user): string
    {
        $payload = [
            'iss' => config('app.name'),
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + ($this->refreshTokenTTL * 60),
            'jti' => uniqid(),
            'type' => 'refresh'
        ];

        $token = JWT::encode($payload, $this->secretKey, $this->algorithm);

        Cache::put("refresh_token:{$user->id}:{$payload['jti']}", true, $this->refreshTokenTTL);

        return $token;
    }

    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array) $decoded;
        } catch (ExpiredException $e) {
            throw new \Exception('Token expired', 401);
        } catch (SignatureInvalidException $e) {
            throw new \Exception('Invalid token signature', 401);
        } catch (\Exception $e) {
            throw new \Exception('Invalid token', 401);
        }
    }

    public function refreshAccessToken(string $refreshToken): array
    {
        $payload = $this->validateToken($refreshToken);

        if ($payload['type'] !== 'refresh') {
            throw new \Exception('Invalid refresh token', 401);
        }

        $cacheKey = "refresh_token:{$payload['sub']}:{$payload['jti']}";
        if (!Cache::has($cacheKey)) {
            throw new \Exception('Refresh token revoked', 401);
        }

        $user = User::find($payload['sub']);
        if (!$user) {
            throw new \Exception('User not found', 404);
        }

        $newAccessToken = $this->generateAccessToken($user);
        $newRefreshToken = $this->generateRefreshToken($user);

        Cache::forget($cacheKey);

        return [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'Bearer',
            'expires_in' => $this->accessTokenTTL * 60,
            'user' => $user
        ];
    }

    public function revokeRefreshToken(string $refreshToken): void
    {
        try {
            $payload = $this->validateToken($refreshToken);
            $cacheKey = "refresh_token:{$payload['sub']}:{$payload['jti']}";
            Cache::forget($cacheKey);
        } catch (\Exception $e) {
            // Token já inválido
        }
    }

    public function revokeAllUserTokens(int $userId): void
    {
        $pattern = "refresh_token:{$userId}:*";
        $keys = Cache::getRedis()->keys($pattern);

        if (!empty($keys)) {
            Cache::getRedis()->del($keys);
        }
    }
}
