<?php

namespace App\Http\Middleware;

use App\Services\ProxyCheckService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProxyCheckMiddleware
{
    private ProxyCheckService $proxyCheck;

    public function __construct(ProxyCheckService $proxyCheck)
    {
        $this->proxyCheck = $proxyCheck;
    }

    public function handle(Request $request, Closure $next, ...$options)
    {
        $ip = $this->getRealIpAddress($request);
        $config = $this->parseOptions($options);

        if ($this->shouldBypassSecurityCheck($ip)) {
            $this->logBypassedCheck($request, $ip, 'IP whitelisted or development bypass');
            return $next($request);
        }

        $ipInfo = $this->proxyCheck->checkIp($ip);
        $this->logSecurityCheck($request, $ip, $ipInfo, $config);

        if ($this->shouldBlockRequest($ipInfo, $config)) {
            $this->logBlockedRequest($request, $ip, $ipInfo, $config);
            return $this->createBlockResponse($ipInfo, $config);
        }

        $this->attachSecurityInfoToRequest($request, $ip, $ipInfo);

        return $next($request);
    }

    private function getRealIpAddress(Request $request): string
    {
        $trustedHeaders = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];

        foreach ($trustedHeaders as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = array_map('trim', explode(',', $_SERVER[$header]));
                $ip = $ips[0];

                if ($this->isValidPublicIp($ip)) {
                    return $ip;
                }
            }
        }

        return $request->ip();
    }

    private function isValidPublicIp(string $ip): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        if (app()->environment('local', 'testing')) {
            return true;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    private function shouldBypassSecurityCheck(string $ip): bool
    {
        if ($this->isWhitelisted($ip)) {
            return true;
        }

        if (app()->environment('local') &&
            config('security.development.bypass_localhost', true) &&
            $this->isLocalhost($ip)) {
            return true;
        }

        return false;
    }

    private function isWhitelisted(string $ip): bool
    {
        $whitelist = config('security.ip_whitelist', []);

        foreach ($whitelist as $whitelistedIp) {
            if (str_contains($whitelistedIp, '/')) {
                if ($this->ipInRange($ip, $whitelistedIp)) {
                    return true;
                }
            } elseif ($ip === $whitelistedIp) {
                return true;
            }
        }

        return false;
    }

    private function isLocalhost(string $ip): bool
    {
        return in_array($ip, ['127.0.0.1', '::1', 'localhost'], true);
    }

    private function ipInRange(string $ip, string $cidr): bool
    {
        [$subnet, $mask] = explode('/', $cidr);

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return (ip2long($ip) & ~((1 << (32 - $mask)) - 1)) === ip2long($subnet);
        }

        return false;
    }

    private function parseOptions(array $options): array
    {
        $config = [
            'block_proxies' => false,
            'block_vpns' => false,
            'block_high_risk' => false,
            'risk_threshold' => config('security.default_risk_threshold', 75),
            'log_only' => false
        ];

        foreach ($options as $option) {
            match ($option) {
                'block-proxies' => $config['block_proxies'] = true,
                'block-vpns' => $config['block_vpns'] = true,
                'block-high-risk' => $config['block_high_risk'] = true,
                'log-only' => $config['log_only'] = true,
                default => $this->parseRiskThreshold($option, $config)
            };
        }

        return $config;
    }

    private function parseRiskThreshold(string $option, array &$config): void
    {
        if (str_starts_with($option, 'risk-')) {
            $threshold = (int) str_replace('risk-', '', $option);
            if ($threshold >= 0 && $threshold <= 100) {
                $config['risk_threshold'] = $threshold;
            }
        }
    }

    private function shouldBlockRequest(array $ipInfo, array $config): bool
    {
        if ($config['log_only'] || !($ipInfo['success'] ?? false)) {
            return false;
        }

        return match (true) {
            $config['block_proxies'] && ($ipInfo['is_proxy'] ?? false) => true,
            $config['block_vpns'] && ($ipInfo['is_vpn'] ?? false) => true,
            $config['block_high_risk'] && ($ipInfo['risk_score'] ?? 0) >= $config['risk_threshold'] => true,
            default => false
        };
    }

    private function createBlockResponse(array $ipInfo, array $config): \Illuminate\Http\JsonResponse
    {
        $reasons = [];

        if ($config['block_proxies'] && ($ipInfo['is_proxy'] ?? false)) {
            $reasons[] = 'Proxy server detected';
        }

        if ($config['block_vpns'] && ($ipInfo['is_vpn'] ?? false)) {
            $reasons[] = 'VPN service detected';
        }

        if ($config['block_high_risk'] && ($ipInfo['risk_score'] ?? 0) >= $config['risk_threshold']) {
            $reasons[] = sprintf('High risk IP address (score: %d)', $ipInfo['risk_score'] ?? 0);
        }

        return response()->json([
            'success' => false,
            'error' => 'Access denied due to security policy',
            'message' => 'Your connection has been blocked by our security system',
            'details' => [
                'code' => 'IP_SECURITY_BLOCK',
                'reasons' => $reasons,
                'country' => $ipInfo['country'] ?? 'Unknown',
                'risk_score' => $ipInfo['risk_score'] ?? 0,
                'timestamp' => now()->toISOString()
            ]
        ], 403);
    }

    private function attachSecurityInfoToRequest(Request $request, string $ip, array $ipInfo): void
    {
        $request->merge([
            'security_info' => [
                'real_ip' => $ip,
                'ip_info' => $ipInfo,
                'checked_at' => now()->toISOString()
            ]
        ]);
    }

    private function logSecurityCheck(Request $request, string $ip, array $ipInfo, array $config): void
    {
        if (!config('security.log_all_checks', true)) {
            return;
        }

        $logData = [
            'event' => 'ip_security_check',
            'ip' => $ip,
            'route' => $request->route()?->getName() ?? 'unknown',
            'method' => $request->method(),
            'url' => $request->url(),
            'user_agent' => $request->userAgent(),
            'user_id' => $request->user()?->id,
            'user_email' => $request->user()?->email,
            'security_result' => [
                'is_proxy' => $ipInfo['is_proxy'] ?? false,
                'is_vpn' => $ipInfo['is_vpn'] ?? false,
                'risk_score' => $ipInfo['risk_score'] ?? 0,
                'country' => $ipInfo['country'] ?? 'Unknown',
                'provider' => $ipInfo['provider'] ?? 'Unknown'
            ],
            'config' => $config,
            'timestamp' => now()->toISOString()
        ];

        Log::channel('security')->info('IP security check performed', $logData);
    }

    private function logBypassedCheck(Request $request, string $ip, string $reason): void
    {
        if (config('security.development.log_level') === 'debug') {
            Log::channel('security')->debug('Security check bypassed', [
                'event' => 'security_check_bypassed',
                'ip' => $ip,
                'reason' => $reason,
                'route' => $request->route()?->getName() ?? 'unknown',
                'timestamp' => now()->toISOString()
            ]);
        }
    }

    private function logBlockedRequest(Request $request, string $ip, array $ipInfo, array $config): void
    {
        Log::channel('security')->warning('Request blocked by security middleware', [
            'event' => 'request_blocked',
            'ip' => $ip,
            'route' => $request->route()?->getName() ?? 'unknown',
            'method' => $request->method(),
            'url' => $request->url(),
            'user_agent' => $request->userAgent(),
            'user_id' => $request->user()?->id,
            'user_email' => $request->user()?->email,
            'block_reason' => [
                'is_proxy' => $ipInfo['is_proxy'] ?? false,
                'is_vpn' => $ipInfo['is_vpn'] ?? false,
                'risk_score' => $ipInfo['risk_score'] ?? 0,
                'threshold' => $config['risk_threshold']
            ],
            'ip_details' => [
                'country' => $ipInfo['country'] ?? 'Unknown',
                'provider' => $ipInfo['provider'] ?? 'Unknown'
            ],
            'timestamp' => now()->toISOString()
        ]);
    }
}
