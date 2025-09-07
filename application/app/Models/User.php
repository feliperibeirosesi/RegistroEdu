<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'avatar',
        'password',
        'provider',
        'provider_id',
        'last_login_at',
        'last_login_ip',
        'ip_info',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'ip_info' => 'array',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }

    public function activeRefreshTokens()
    {
        return $this->refreshTokens()->valid();
    }

    public function getActiveSessionsCount(): int
    {
        return $this->activeRefreshTokens()->count();
    }

    public function revokeAllSessions(): int
    {
        return $this->refreshTokens()->active()->update(['is_active' => false]);
    }

    public function hasActiveSessionFrom(string $ipAddress): bool
    {
        return $this->activeRefreshTokens()
            ->where('ip_address', $ipAddress)
            ->exists();
    }

    public function setAvatarAttribute($value)
    {
        $this->attributes['avatar'] = $value ?: 'default-avatar.png';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
