<?php

namespace App\Utils;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Psr\Log\LogLevel;

class Tools
{
    public static function res(string $message, int $statusCode, array $data = []): JsonResponse
    {
        $response = ['message' => $message];

        if (! empty($data)) {
            $response = array_merge($response, $data);
        }

        return response()->json($response, $statusCode);
    }

    public static function success(string $message, array $data = []): JsonResponse
    {
        return self::res($message, 200, $data);
    }

    public static function error(string $message, int $statusCode = 400, array $details = []): JsonResponse
    {
        $data = [];
        if (! empty($details)) {
            $data['details'] = $details;
        }

        return self::res($message, $statusCode, $data);
    }

    public static function validationError(string $message, array $errors): JsonResponse
    {
        return self::res($message, 422, ['errors' => $errors]);
    }

    public static function logAuthEvent(string $level, string $message, array $context = []): void
    {
        $context = array_merge($context, [
            'component' => 'authentication',
            'timestamp' => now()->toISOString(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        Log::log($level, "[AUTH] {$message}", $context);
    }

    public static function logSecurityEvent(string $level, string $message, array $context = []): void
    {
        $context = array_merge($context, [
            'component' => 'security',
            'timestamp' => now()->toISOString(),
            'ip' => request()->get('real_ip', request()->ip()),
            'route' => request()->route()?->getName() ?? 'unknown',
        ]);

        Log::log($level, "[SECURITY] {$message}", $context);
    }

    public static function logSystemEvent(string $level, string $message, array $context = []): void
    {
        $context = array_merge($context, [
            'component' => 'system',
            'timestamp' => now()->toISOString(),
        ]);

        Log::log($level, "[SYSTEM] {$message}", $context);
    }

    public static function getRealIp(): string
    {
        $trustedHeaders = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR',
        ];

        foreach ($trustedHeaders as $header) {
            if (! empty($_SERVER[$header])) {
                $ips = array_map('trim', explode(',', $_SERVER[$header]));
                $ip = $ips[0];

                if (self::isValidPublicIp($ip)) {
                    return $ip;
                }
            }
        }

        return request()->ip();
    }

    public static function isValidPublicIp(string $ip): bool
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        if (app()->environment('local', 'testing')) {
            return true;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    public static function getUserContext(string|int|null $userId = null): array
    {
        $user = request()->user();

        return [
            'user_id' => $userId ?? $user?->id,
            'user_email' => $user?->email,
            'session_id' => session()->getId(),
        ];
    }

    public static function tokenResponse(string $message, array $tokenData): JsonResponse
    {
        return self::success($message, $tokenData)
            ->cookie('access_token', $tokenData['access_token'], config('jwt.access_ttl', 60))
            ->cookie('refresh_token', $tokenData['refresh_token'], config('jwt.refresh_ttl', 20160), null, null, true, true);
    }

    public static function securityBlockResponse(array $reasons, array $ipInfo): JsonResponse
    {
        return self::error('Access denied due to security policy', 403, [
            'code' => 'IP_SECURITY_BLOCK',
            'reasons' => $reasons,
            'country' => $ipInfo['country'] ?? 'Unknown',
            'risk_score' => $ipInfo['risk_score'] ?? 0,
            'timestamp' => now()->toISOString(),
        ]);
    }

    public static function getIpContext(array $ipInfo = []): array
    {
        return [
            'ip_address' => self::getRealIp(),
            'country' => $ipInfo['country'] ?? 'Unknown',
            'risk_score' => $ipInfo['risk_score'] ?? 0,
            'is_proxy' => $ipInfo['is_proxy'] ?? false,
            'is_vpn' => $ipInfo['is_vpn'] ?? false,
            'provider' => $ipInfo['provider'] ?? 'Unknown',
        ];
    }

    public static function logLoginAttempt(bool $success, string $email, array $ipInfo = [], ?string $reason = null): void
    {
        $level = $success ? LogLevel::INFO : LogLevel::WARNING;
        $message = $success ? 'Login successful' : 'Login failed';

        $context = array_merge(
            self::getIpContext($ipInfo),
            [
                'email' => $email,
                'success' => $success,
                'environment' => app()->environment(),
            ]
        );

        if ($reason) {
            $context['failure_reason'] = $reason;
        }

        self::logAuthEvent($level, $message, $context);
    }

    public static function logIpSecurityCheck(string $ip, array $ipInfo, array $config, bool $blocked = false): void
    {
        $level = $blocked ? LogLevel::WARNING : LogLevel::INFO;
        $message = $blocked ? 'IP security check - Access blocked' : 'IP security check - Access allowed';

        $context = array_merge(
            self::getIpContext($ipInfo),
            [
                'security_config' => $config,
                'blocked' => $blocked,
                'check_result' => $ipInfo,
            ]
        );

        self::logSecurityEvent($level, $message, $context);
    }
}
