<?php
return [
    'secret' => env('JWT_SECRET'),
    'algorithm' => env('JWT_ALGORITHM', 'HS256'),
    'access_ttl' => env('JWT_ACCESS_TTL', 60),
    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),
];
