<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProxyCheckService
{
    private Client $client;
    private ?string $apiKey;
    private string $baseUrl = 'http://proxycheck.io/v2/';
    private bool $enabled;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 10]);
        $this->apiKey = config('services.proxycheck.key');
        $this->enabled = config('services.proxycheck.enabled', true);
    }

    public function checkIp(string $ip): array
    {
        if (!$this->enabled) {
            return $this->getDefaultResponse($ip);
        }

        $cacheKey = "proxycheck_v2_{$ip}";

        return Cache::remember($cacheKey, 7200, function () use ($ip) {
            return $this->performApiCheck($ip);
        });
    }

    public function checkMultipleIps(array $ips): array
    {
        if (!$this->enabled) {
            return array_map([$this, 'getDefaultResponse'], $ips);
        }

        $results = [];
        $uncachedIps = [];

        foreach ($ips as $ip) {
            $cacheKey = "proxycheck_v2_{$ip}";
            if (Cache::has($cacheKey)) {
                $results[$ip] = Cache::get($cacheKey);
            } else {
                $uncachedIps[] = $ip;
            }
        }

        if (!empty($uncachedIps)) {
            $batchResults = $this->performBatchApiCheck($uncachedIps);

            foreach ($batchResults as $ip => $result) {
                $cacheKey = "proxycheck_v2_{$ip}";
                Cache::put($cacheKey, $result, 7200);
                $results[$ip] = $result;
            }
        }

        return $results;
    }

    private function performApiCheck(string $ip): array
    {
        try {
            $queryParams = [
                'vpn' => 1,
                'asn' => 1,
                'node' => 1,
                'time' => 1,
                'inf' => 0,
                'risk' => 1,
                'port' => 1,
                'seen' => 1,
                'days' => 7,
                'tag' => 'laravel-app'
            ];

            if ($this->apiKey) {
                $queryParams['key'] = $this->apiKey;
            }

            $response = $this->client->get($this->baseUrl . $ip, [
                'query' => $queryParams
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'ok' && isset($data[$ip])) {
                return $this->normalizeResponse($ip, $data[$ip]);
            }

            return $this->getDefaultResponse($ip, 'API returned invalid response');

        } catch (\Exception $e) {
            Log::warning('ProxyCheck API Error', [
                'ip' => $ip,
                'error' => $e->getMessage()
            ]);

            return $this->getDefaultResponse($ip, $e->getMessage());
        }
    }

    private function performBatchApiCheck(array $ips): array
    {
        try {
            $ipList = implode(',', array_slice($ips, 0, 100));

            $queryParams = [
                'vpn' => 1,
                'asn' => 1,
                'risk' => 1,
                'tag' => 'laravel-batch'
            ];

            if ($this->apiKey) {
                $queryParams['key'] = $this->apiKey;
            }

            $response = $this->client->get($this->baseUrl . $ipList, [
                'query' => $queryParams
            ]);

            $data = json_decode($response->getBody(), true);
            $results = [];

            if ($data['status'] === 'ok') {
                foreach ($ips as $ip) {
                    if (isset($data[$ip])) {
                        $results[$ip] = $this->normalizeResponse($ip, $data[$ip]);
                    } else {
                        $results[$ip] = $this->getDefaultResponse($ip);
                    }
                }
            }

            return $results;

        } catch (\Exception $e) {
            Log::warning('ProxyCheck Batch API Error', [
                'ips' => $ips,
                'error' => $e->getMessage()
            ]);

            $results = [];
            foreach ($ips as $ip) {
                $results[$ip] = $this->getDefaultResponse($ip, $e->getMessage());
            }
            return $results;
        }
    }

    private function normalizeResponse(string $ip, array $data): array
    {
        return [
            'success' => true,
            'ip' => $ip,
            'is_proxy' => ($data['proxy'] ?? 'no') === 'yes',
            'is_vpn' => isset($data['type']) && strtolower($data['type']) === 'vpn',
            'risk_score' => (int) ($data['risk'] ?? 0),
            'country' => $data['country'] ?? 'Unknown',
            'country_code' => $data['isocode'] ?? 'XX',
            'provider' => $data['provider'] ?? 'Unknown',
            'asn' => $data['asn'] ?? null,
            'city' => $data['city'] ?? null,
            'region' => $data['region'] ?? null,
            'timezone' => $data['timezone'] ?? null,
            'last_seen' => isset($data['last seen']) ? (int) $data['last seen'] : null,
            'checked_at' => now()->toISOString()
        ];
    }

    private function getDefaultResponse(string $ip, ?string $error = null): array
    {
        return [
            'success' => false,
            'ip' => $ip,
            'is_proxy' => false,
            'is_vpn' => false,
            'risk_score' => 0,
            'country' => 'Unknown',
            'country_code' => 'XX',
            'provider' => 'Unknown',
            'error' => $error,
            'checked_at' => now()->toISOString()
        ];
    }

    public function isHighRisk(string $ip, int $threshold = 75): bool
    {
        $result = $this->checkIp($ip);
        return $result['risk_score'] >= $threshold;
    }

    public function isProxyOrVpn(string $ip): bool
    {
        $result = $this->checkIp($ip);
        return $result['is_proxy'] || $result['is_vpn'];
    }

    public function getLocationInfo(string $ip): array
    {
        $result = $this->checkIp($ip);

        return [
            'country' => $result['country'],
            'country_code' => $result['country_code'],
            'city' => $result['city'],
            'region' => $result['region'],
            'timezone' => $result['timezone']
        ];
    }
}
