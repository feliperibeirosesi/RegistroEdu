<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Utils\Tools;

class HealthController extends Controller
{
    public function basic()
    {
        return Tools::success('System is running', [
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
            'version' => config('app.version', '1.0.0')
        ]);
    }

    public function detailed()
    {
        $checks = [];

        try {
            DB::connection()->getPdo();
            $checks['database'] = ['status' => 'ok', 'response_time' => $this->measureTime(fn() => DB::select('SELECT 1'))];
        } catch (\Exception $e) {
            $checks['database'] = ['status' => 'error', 'message' => $e->getMessage()];
        }

        try {
            $start = microtime(true);
            Cache::store('redis')->put('health_ping', time(), 10);
            $responseTime = round((microtime(true) - $start) * 1000, 2);
            $checks['redis'] = ['status' => 'ok', 'response_time' => $responseTime . 'ms'];
        } catch (\Exception $e) {
            $checks['redis'] = ['status' => 'error', 'message' => $e->getMessage()];
        }

        $checks['system'] = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . 'MB',
            'uptime' => $this->getSystemUptime()
        ];

        return Tools::success('System health check', [
            'checks' => $checks,
            'timestamp' => now()->toISOString()
        ]);
    }

    private function measureTime(callable $callback): string
    {
        $start = microtime(true);
        $callback();
        return round((microtime(true) - $start) * 1000, 2) . 'ms';
    }

    private function getSystemUptime(): string
    {
        if (PHP_OS_FAMILY === 'Linux') {
            $uptime = file_get_contents('/proc/uptime');
            $seconds = (int) explode(' ', $uptime)[0];
            return gmdate('H:i:s', $seconds);
        }
        return 'N/A';
    }
}
