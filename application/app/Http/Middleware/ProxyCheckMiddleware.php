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

        if ($this->isWhitelisted($ip)) {
            return $next($request);
        }

        $config = $this->parseOptions($options);
        $ipInfo = $this->proxyCheck->checkIp($ip);

        $this->logSecurityCheck($request, $ip, $ipInfo, $config);

        if ($this->shouldBlock($ipInfo, $config)) {
            return $this->createBlockResponse($ipInfo, $config);
        }

        $request->merge([
            'ip_info' => $ipInfo,
            'real_ip' => $ip
        ]);

        return $next($request);
    }

    private function getRealIpAddress(Request $request): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $request->ip();
    }

    private function isWhitelisted(string $ip): bool
    {
        $whitelist = config('security.ip_whitelist', []);
        return in_array($ip, $whitelist, true);
    }

    private function parseOptions(array $options): array
    {
        $config = [
            'block_proxies' => false,
            'block_vpns' => false,
            'block_high_risk' => false,
            'risk_threshold' => 75,
            'log_only' => false
        ];

        foreach ($options as $option) {
            switch ($option) {
                case 'block-proxies':
                    $config['block_proxies'] = true;
                    break;
                case 'block-vpns':
                    $config['block_vpns'] = true;
                    break;
                case 'block-high-risk':
                    $config['block_high_risk'] = true;
                    break;
                case 'log-only':
                    $config['log_only'] = true;
                    break;
                default:
                    if (str_starts_with($option, 'risk-')) {
                        $config['risk_threshold'] = (int) str_replace('risk-', '', $option);
                    }
            }
        }

        return $config;
    }

    private function shouldBlock(array $ipInfo, array $config): bool
    {
        if ($config['log_only'] || !$ipInfo['success']) {
            return false;
        }

        if ($config['block_proxies'] && $ipInfo['is_proxy']) {
            return true;
        }

        if ($config['block_vpns'] && $ipInfo['is_vpn']) {
            return true;
        }

        if ($config['block_high_risk'] && $ipInfo['risk_score'] >= $config['risk_threshold']) {
            return true;
        }

        return false;
    }

    private function createBlockResponse(array $ipInfo, array $config): \Illuminate\Http\JsonResponse
    {
        $reason = [];

        if ($config['block_proxies'] && $ipInfo['is_proxy']) {
            $reason[] = 'Proxy detected';
        }

        if ($config['block_vpns'] && $ipInfo['is_vpn']) {
            $reason[] = 'VPN detected';
        }

        if ($config['block_high_risk'] && $ipInfo['risk_score'] >= $config['risk_threshold']) {
            $reason[] = "High risk IP (score: {$ipInfo['risk_score']})";
        }

        return response()->json([
            'error' => 'Access denied',
            'reason' => implode(', ', $reason),
            'code' => 'IP_BLOCKED',
            'country' => $ipInfo['country'] ?? 'Unknown',
            'risk_score' => $ipInfo['risk_score'] ?? 0
        ], 403);
    }

    private function logSecurityCheck(Request $request, string $ip, array $ipInfo, array $config): void
    {
        $user = $request->user();

        Log::channel('security')->info('IP Security Check', [
            'ip' => $ip,
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'route' => $request->route()?->getName(),
            'url' => $request->url(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
            'is_proxy' => $ipInfo['is_proxy'] ?? false,
            'is_vpn' => $ipInfo['is_vpn'] ?? false,
            'risk_score' => $ipInfo['risk_score'] ?? 0,
            'country' => $ipInfo['country'] ?? 'Unknown',
            'provider' => $ipInfo['provider'] ?? 'Unknown',
            'config' => $config,
            'timestamp' => now()->toISOString()
        ]);
    }
}
