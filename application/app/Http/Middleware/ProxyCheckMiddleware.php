<?php

namespace App\Http\Middleware;

use App\Services\ProxyCheckService;
use App\Models\IpSecurityCheck;
use App\Utils\Tools;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Psr\Log\LogLevel;

class ProxyCheckMiddleware
{
    private ProxyCheckService $proxyCheck;

    public function __construct(ProxyCheckService $proxyCheck)
    {
        $this->proxyCheck = $proxyCheck;
    }

    public function handle(Request $request, Closure $next, ...$options)
    {
        $ip = Tools::getRealIp();
        $config = $this->parseOptions($options);

        $cachedResult = $this->getCachedSecurityCheck($ip);
        if ($cachedResult) {
            return $this->handleCachedResult($request, $next, $ip, $cachedResult, $config);
        }

        if ($this->shouldBypassSecurityCheck($ip)) {
            Tools::logSecurityEvent(LogLevel::DEBUG, "Security check bypassed", [
                'ip_address' => $ip,
                'reason' => 'IP whitelisted or development bypass'
            ]);

            $this->storeSecurityResult($ip, ['bypassed' => true, 'reason' => 'whitelisted']);
            return $next($request);
        }

        $ipInfo = $this->proxyCheck->checkIp($ip);

        $shouldBlock = $this->shouldBlockRequest($ipInfo, $config);

        Tools::logIpSecurityCheck($ip, $ipInfo, $config, $shouldBlock);

        if ($shouldBlock) {
            $blockReasons = $this->getBlockReasons($ipInfo, $config);

            $this->storeSecurityResult($ip, array_merge($ipInfo, [
                'blocked' => true,
                'block_reason' => $blockReasons
            ]));

            return $this->createBlockResponse($ipInfo, $config);
        }

        $this->storeSecurityResult($ip, array_merge($ipInfo, ['allowed' => true]));
        $this->attachSecurityInfoToRequest($request, $ip, $ipInfo);

        return $next($request);
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

        if (empty($whitelist)) {
            return false;
        }

        if (in_array('0.0.0.0/0', $whitelist) && app()->environment('local')) {
            return true;
        }

        if (!app()->environment('local')) {
            $whitelist = array_filter($whitelist, fn($ip) => $ip !== '0.0.0.0/0');
        }

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

    private function createBlockResponse(array $ipInfo, array $config)
    {
        $reasons = $this->getHumanReadableReasons($ipInfo, $config);
        return Tools::securityBlockResponse($reasons, $ipInfo);
    }

    private function getHumanReadableReasons(array $ipInfo, array $config): array
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

        return $reasons;
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

    private function getCachedSecurityCheck(string $ip): ?array
    {
        $cached = Cache::get("security_check_$ip");
        if ($cached && $cached['expires_at'] > now()) {
            return $cached;
        }

        $dbResult = IpSecurityCheck::where('ip_address', $ip)
            ->where('checked_at', '>', now()->subHours(24))
            ->latest()
            ->first();

        if ($dbResult) {
            $data = [
                'ip_info' => $dbResult->security_data,
                'checked_at' => $dbResult->checked_at,
                'expires_at' => $dbResult->checked_at->addHours(24)
            ];

            Cache::put("security_check_$ip", $data, now()->addHours(24));
            return $data;
        }

        return null;
    }

    private function handleCachedResult(Request $request, Closure $next, string $ip, array $cachedResult, array $config)
    {
        $ipInfo = $cachedResult['ip_info'];

        Tools::logSecurityEvent(LogLevel::DEBUG, "Using cached security check", [
            'ip_address' => $ip,
            'cached_at' => $cachedResult['checked_at']
        ]);

        if (isset($ipInfo['blocked']) && $ipInfo['blocked']) {
            return $this->createBlockResponse($ipInfo, $config);
        }

        $this->attachSecurityInfoToRequest($request, $ip, $ipInfo);
        return $next($request);
    }

    private function storeSecurityResult(string $ip, array $securityData): void
    {
        try {
            IpSecurityCheck::updateOrCreate(
                ['ip_address' => $ip],
                [
                    'security_data' => $securityData,
                    'checked_at' => now(),
                    'risk_score' => $securityData['risk_score'] ?? 0,
                    'country' => $securityData['country'] ?? 'Unknown',
                    'is_blocked' => $securityData['blocked'] ?? false
                ]
            );

            Cache::put("security_check_$ip", [
                'ip_info' => $securityData,
                'checked_at' => now(),
                'expires_at' => now()->addHours(24)
            ], now()->addHours(24));

        } catch (\Exception $e) {
            Tools::logSystemEvent(LogLevel::ERROR, "Failed to store security result", [
                'ip_address' => $ip,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getBlockReasons(array $ipInfo, array $config): array
    {
        $reasons = [];

        if ($config['block_proxies'] && ($ipInfo['is_proxy'] ?? false)) {
            $reasons[] = 'proxy_detected';
        }

        if ($config['block_vpns'] && ($ipInfo['is_vpn'] ?? false)) {
            $reasons[] = 'vpn_detected';
        }

        if ($config['block_high_risk'] && ($ipInfo['risk_score'] ?? 0) >= $config['risk_threshold']) {
            $reasons[] = 'high_risk_score';
        }

        return $reasons;
    }

    public static function getIpSecurityInfo(string $ip): ?array
    {
        $cached = Cache::get("security_check_$ip");
        if ($cached && $cached['expires_at'] > now()) {
            return $cached['ip_info'];
        }

        $dbResult = IpSecurityCheck::where('ip_address', $ip)
            ->where('checked_at', '>', now()->subHours(24))
            ->latest()
            ->first();

        return $dbResult ? $dbResult->security_data : null;
    }
}
