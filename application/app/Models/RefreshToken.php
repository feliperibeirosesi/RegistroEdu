<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class RefreshToken extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'jti',
        'expires_at',
        'ip_address',
        'user_agent',
        'is_active',
        'last_used_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = now();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeValid($query)
    {
        return $query->active()->where('expires_at', '>', now());
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    public function revoke(): bool
    {
        return $this->update(['is_active' => false]);
    }

    public function markAsUsed(): bool
    {
        return $this->update(['last_used_at' => now()]);
    }

    public static function findByJti(string $jti): ?self
    {
        return static::where('jti', $jti)->first();
    }

    public static function revokeAllForUser(string $userId): int
    {
        return static::forUser($userId)->active()->update(['is_active' => false]);
    }

    public static function cleanupExpired(): int
    {
        return static::expired()->delete();
    }

    public static function getActiveCountForUser(string $userId): int
    {
        return static::forUser($userId)->valid()->count();
    }
}
