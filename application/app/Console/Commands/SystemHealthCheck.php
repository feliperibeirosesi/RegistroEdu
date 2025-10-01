<?php

namespace App\Console\Commands;

use App\Models\RefreshToken;
use App\Models\User;
use App\Services\JWTService;
use App\Services\ProxyCheckService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SystemHealthCheck extends Command
{
    protected $signature = 'system:health-check {--detailed : Show detailed test results}';

    protected $description = 'Comprehensive system health check';

    public function handle()
    {
        $this->info('🔍 Starting System Health Check...');
        $this->newLine();

        $allPassed = true;
        $results = [];

        // 1. Database Connection
        $results['database'] = $this->checkDatabase();

        // 2. Redis Connection
        $results['redis'] = $this->checkRedis();

        // 3. Tables Structure
        $results['tables'] = $this->checkTables();

        // 4. Security Configuration
        $results['security'] = $this->checkSecurityConfig();

        // 5. JWT Service
        $results['jwt'] = $this->checkJWTService();

        // 6. ProxyCheck Service
        $results['proxycheck'] = $this->checkProxyCheckService();

        // 7. OAuth Configuration
        $results['oauth'] = $this->checkOAuthConfig();

        // Summary
        $this->newLine();
        $this->displaySummary($results);

        foreach ($results as $result) {
            if (! $result['status']) {
                $allPassed = false;
                break;
            }
        }

        return $allPassed ? 0 : 1;
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            $this->displayCheck('Database Connection', true);

            return ['status' => true, 'message' => 'Connected'];
        } catch (\Exception $e) {
            $this->displayCheck('Database Connection', false, $e->getMessage());

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    private function checkRedis(): array
    {
        try {
            Cache::store('redis')->put('health_check', 'test', 10);
            $value = Cache::store('redis')->get('health_check');

            if ($value === 'test') {
                $this->displayCheck('Redis Connection', true);

                return ['status' => true, 'message' => 'Connected and working'];
            } else {
                $this->displayCheck('Redis Connection', false, 'Cannot read/write');

                return ['status' => false, 'message' => 'Cannot read/write'];
            }
        } catch (\Exception $e) {
            $this->displayCheck('Redis Connection', false, $e->getMessage());

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    private function checkTables(): array
    {
        $requiredTables = ['users', 'sessions', 'refresh_tokens', 'ip_security_checks'];
        $missingTables = [];

        foreach ($requiredTables as $table) {
            try {
                DB::table($table)->limit(1)->get();
            } catch (\Exception $e) {
                $missingTables[] = $table;
            }
        }

        if (empty($missingTables)) {
            $this->displayCheck('Database Tables', true);

            return ['status' => true, 'message' => 'All tables exist'];
        } else {
            $message = 'Missing: '.implode(', ', $missingTables);
            $this->displayCheck('Database Tables', false, $message);

            return ['status' => false, 'message' => $message];
        }
    }

    private function checkSecurityConfig(): array
    {
        $issues = [];

        // Check IP Whitelist
        $whitelist = config('security.ip_whitelist', []);
        if (in_array('0.0.0.0/0', $whitelist) && ! app()->environment('local')) {
            $issues[] = 'IP whitelist allows all IPs (0.0.0.0/0) in production';
        }

        // Check JWT Secret
        if (! config('jwt.secret')) {
            $issues[] = 'JWT secret not configured';
        }

        // Check security settings
        $securitySettings = [
            'security.login.block_proxies',
            'security.login.block_vpns',
            'security.login.block_high_risk',
        ];

        foreach ($securitySettings as $setting) {
            if (config($setting) === null) {
                $issues[] = "Setting {$setting} not configured";
            }
        }

        if (empty($issues)) {
            $this->displayCheck('Security Configuration', true);

            return ['status' => true, 'message' => 'All security settings OK'];
        } else {
            $message = implode('; ', $issues);
            $this->displayCheck('Security Configuration', false, $message);

            return ['status' => false, 'message' => $message];
        }
    }

    private function checkJWTService(): array
    {
        try {
            $jwtService = app(JWTService::class);

            // Create test user if not exists
            $testUser = User::firstOrCreate(
                ['email' => 'test@system.health'],
                [
                    'name' => 'Health Check User',
                    'password' => bcrypt('test123'),
                    'provider' => 'system',
                ]
            );

            // Test token generation
            $accessToken = $jwtService->generateAccessToken($testUser);
            $refreshToken = $jwtService->generateRefreshToken($testUser);

            // Test token validation
            $payload = $jwtService->validateToken($accessToken);

            if ($payload['sub'] === $testUser->id) {
                // Cleanup
                RefreshToken::where('user_id', $testUser->id)->delete();
                if ($testUser->email === 'test@system.health') {
                    $testUser->delete();
                }

                $this->displayCheck('JWT Service', true);

                return ['status' => true, 'message' => 'Token generation and validation working'];
            } else {
                throw new \Exception('Token validation failed');
            }

        } catch (\Exception $e) {
            $this->displayCheck('JWT Service', false, $e->getMessage());

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    private function checkProxyCheckService(): array
    {
        try {
            $proxyCheck = app(ProxyCheckService::class);

            // Test with a known good IP (Google DNS)
            $result = $proxyCheck->checkIp('8.8.8.8');

            if (isset($result['ip']) && $result['ip'] === '8.8.8.8') {
                $this->displayCheck('ProxyCheck Service', true);

                return ['status' => true, 'message' => 'API responding correctly'];
            } else {
                $this->displayCheck('ProxyCheck Service', false, 'Invalid API response');

                return ['status' => false, 'message' => 'Invalid API response'];
            }

        } catch (\Exception $e) {
            $this->displayCheck('ProxyCheck Service', false, $e->getMessage());

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    private function checkOAuthConfig(): array
    {
        $issues = [];

        $requiredConfigs = [
            'services.google.client_id' => 'Google Client ID',
            'services.google.client_secret' => 'Google Client Secret',
            'services.google.redirect' => 'Google Redirect URI',
        ];

        foreach ($requiredConfigs as $config => $name) {
            if (! config($config)) {
                $issues[] = "{$name} not configured";
            }
        }

        if (empty($issues)) {
            $this->displayCheck('OAuth Configuration', true);

            return ['status' => true, 'message' => 'All OAuth settings configured'];
        } else {
            $message = implode('; ', $issues);
            $this->displayCheck('OAuth Configuration', false, $message);

            return ['status' => false, 'message' => $message];
        }
    }

    private function displayCheck(string $name, bool $passed, ?string $details = null): void
    {
        $icon = $passed ? '✅' : '❌';
        $status = $passed ? 'PASS' : 'FAIL';

        $line = sprintf('%-30s %s %s', $name, $icon, $status);

        if ($details && ($this->option('detailed') || ! $passed)) {
            $line .= " - {$details}";
        }

        $this->line($line);
    }

    private function displaySummary(array $results): void
    {
        $passed = array_sum(array_column($results, 'status'));
        $total = count($results);

        $this->info("Summary: {$passed}/{$total} checks passed");

        if ($passed === $total) {
            $this->info('System is healthy and ready to go!');
        } else {
            $this->error('System has issues that need attention.');
            $this->newLine();
            $this->error('Failed checks:');

            foreach ($results as $name => $result) {
                if (! $result['status']) {
                    $this->error("  • {$name}: {$result['message']}");
                }
            }
        }
    }
}
