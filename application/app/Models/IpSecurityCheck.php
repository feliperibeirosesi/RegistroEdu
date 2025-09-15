<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpSecurityCheck extends Model
{
    protected $fillable = [
        'ip_address',
        'security_data',
        'risk_score',
        'country',
        'is_blocked',
        'checked_at'
    ];

    protected $casts = [
        'security_data' => 'array',
        'checked_at' => 'datetime',
        'is_blocked' => 'boolean'
    ];

    public static function isIpBlocked(string $ip): bool
    {
        return static::where('ip_address', $ip)
            ->where('is_blocked', true)
            ->where('checked_at', '>', now()->subHours(24))
            ->exists();
    }

    public static function getIpRiskScore(string $ip): int
    {
        $check = static::where('ip_address', $ip)
            ->where('checked_at', '>', now()->subHours(24))
            ->latest()
            ->first();

        return $check ? $check->risk_score : 0;
    }
}
