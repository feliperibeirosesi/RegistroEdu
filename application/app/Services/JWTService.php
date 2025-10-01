<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\RefreshToken;
use App\Utils\Tools;
use Illuminate\Support\Facades\DB;

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
            'jti' => uniqid('acc_', true),
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'type' => 'access'
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function generateRefreshToken(User $user): string
    {
        $jti = uniqid('ref_', true);
        $expiresAt = now()->addMinutes($this->refreshTokenTTL);

        $payload = [
            'iss' => config('app.name'),
            'sub' => $user->id,
            'iat' => time(),
            'exp' => $expiresAt->timestamp,
            'jti' => $jti,
            'type' => 'refresh'
        ];

        $token = JWT::encode($payload, $this->secretKey, $this->algorithm);

        RefreshToken::create([
            'user_id' => $user->id,
            'jti' => $jti,
            'expires_at' => $expiresAt,
            'ip_address' => Tools::getRealIp(),
            'user_agent' => request()->userAgent()
        ]);

        Cache::put(
            "refresh_token:{$user->id}:{$jti}",
            true,
            $this->refreshTokenTTL * 60
        );

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

        $tokenRecord = RefreshToken::findByJti($payload['jti']);

        if (!$tokenRecord || !$tokenRecord->isValid()) {
            throw new \Exception('Refresh token revoked or expired', 401);
        }

        $user = User::find($payload['sub']);
        if (!$user) {
            throw new \Exception('User not found', 404);
        }

        $tokenRecord->markAsUsed();

        $newAccessToken = $this->generateAccessToken($user);
        $newRefreshToken = $this->generateRefreshToken($user);

        $tokenRecord->revoke();
        Cache::forget("refresh_token:{$user->id}:{$payload['jti']}");

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

            $tokenRecord = RefreshToken::findByJti($payload['jti']);
            if ($tokenRecord) {
                $tokenRecord->revoke();
            }

            Cache::forget("refresh_token:{$payload['sub']}:{$payload['jti']}");
        } catch (\Exception $e) {
            // Token já inválido, não precisa fazer nada
        }
    }

    public function revokeAllUserTokens(string $userId): void
    {
        try {
            DB::transaction(function () use ($userId) {
                $count = RefreshToken::revokeAllForUser($userId);

                $this->clearUserTokenCache($userId);

                Tools::logAuthEvent('info', "Revoked {$count} refresh tokens for user", [
                    'user_id' => $userId
                ]);
            });
        } catch (\Exception $e) {
            Tools::logAuthEvent('error', 'Failed to revoke user tokens', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function clearUserTokenCache(string $userId): void
    {
        $pattern = "refresh_token:{$userId}:*";

        try {
            $redis = Cache::getRedis();
            $keys = $redis->keys($pattern);

            if (!empty($keys)) {
                $redis->del($keys);
            }
        } catch (\Exception $e) {
            Tools::logAuthEvent('warning', 'Failed to clear token cache', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getUserActiveSessions(string $userId): array
    {
        return RefreshToken::forUser($userId)
            ->valid()
            ->orderBy('last_used_at', 'desc')
            ->get()
            ->map(function ($token) {
                return [
                    'id' => $token->id,
                    'ip_address' => $token->ip_address,
                    'user_agent' => $token->user_agent,
                    'created_at' => $token->created_at,
                    'last_used_at' => $token->last_used_at,
                    'expires_at' => $token->expires_at
                ];
            })->toArray();
    }

    public function cleanupExpiredTokens(): int
    {
        return RefreshToken::cleanupExpired();
    }
}
