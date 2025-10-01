<?php

return [
    'ip_whitelist' => [
    ],

    'default_risk_threshold' => env('SECURITY_RISK_THRESHOLD', 75),

    'log_all_checks' => env('SECURITY_LOG_ALL_CHECKS', true),

    'login' => [
        'block_proxies' => env('SECURITY_LOGIN_BLOCK_PROXIES', false),
        'block_vpns' => env('SECURITY_LOGIN_BLOCK_VPNS', false),
        'block_high_risk' => env('SECURITY_LOGIN_BLOCK_HIGH_RISK', true),
        'risk_threshold' => env('SECURITY_LOGIN_RISK_THRESHOLD', 85),
        'max_attempts_per_ip' => env('SECURITY_MAX_LOGIN_ATTEMPTS', 10),
        'lockout_duration' => env('SECURITY_LOCKOUT_DURATION', 300),
    ],

    'oauth' => [
        'block_proxies' => env('SECURITY_OAUTH_BLOCK_PROXIES', true),
        'block_vpns' => env('SECURITY_OAUTH_BLOCK_VPNS', true),
        'block_high_risk' => env('SECURITY_OAUTH_BLOCK_HIGH_RISK', true),
        'risk_threshold' => env('SECURITY_OAUTH_RISK_THRESHOLD', 70),

        'allowed_domains' => [
            'production' => [
                'professor.educacao.sp.gov.br',
                'educacao.sp.gov.br',
            ],
            'development' => [
                'gmail.com',
                'teste.com',
            ],
        ],
    ],

    'api' => [
        'block_proxies' => env('SECURITY_API_BLOCK_PROXIES', false),
        'block_vpns' => env('SECURITY_API_BLOCK_VPNS', false),
        'block_high_risk' => env('SECURITY_API_BLOCK_HIGH_RISK', true),
        'risk_threshold' => env('SECURITY_API_RISK_THRESHOLD', 90),
        'rate_limit_strict' => env('SECURITY_API_RATE_LIMIT_STRICT', true),
    ],

    'cache' => [
        'ip_check_ttl' => env('SECURITY_CACHE_IP_TTL', 7200),
        'security_result_ttl' => env('SECURITY_CACHE_RESULT_TTL', 86400),
        'use_database_fallback' => env('SECURITY_USE_DB_FALLBACK', true),
    ],

    'development' => [
        'bypass_localhost' => env('SECURITY_DEV_BYPASS_LOCALHOST', true),
        'log_level' => env('SECURITY_DEV_LOG_LEVEL', 'debug'),
        'fake_high_risk_ips' => env('SECURITY_DEV_FAKE_HIGH_RISK', [
        ]),
    ],

    'notifications' => [
        'enable_security_alerts' => env('SECURITY_ENABLE_ALERTS', true),
        'alert_threshold' => env('SECURITY_ALERT_THRESHOLD', 95),
        'admin_emails' => [
            env('SECURITY_ADMIN_EMAIL', 'admin@empresa.com'),
        ],
        'slack_webhook' => env('SECURITY_SLACK_WEBHOOK'),
    ],

    'trusted_ip_headers' => [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_REAL_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_CLIENT_IP',
        'REMOTE_ADDR',
    ],

    'advanced_blocking' => [
        'enable_geo_blocking' => env('SECURITY_GEO_BLOCKING', false),
        'blocked_countries' => [
        ],
        'allowed_countries' => [
            'BR',
        ],

        'enable_asn_blocking' => env('SECURITY_ASN_BLOCKING', false),
        'blocked_asns' => [
        ],
    ],

    'risk_based_limiting' => [
        'enable' => env('SECURITY_RISK_BASED_LIMITING', true),
        'low_risk_limit' => env('SECURITY_LOW_RISK_LIMIT', 1000),
        'medium_risk_limit' => env('SECURITY_MEDIUM_RISK_LIMIT', 100),
        'high_risk_limit' => env('SECURITY_HIGH_RISK_LIMIT', 10),

        'risk_thresholds' => [
            'low' => 25,
            'medium' => 60,
            'high' => 85,
        ],
    ],

    'honeypot' => [
        'enable' => env('SECURITY_HONEYPOT_ENABLE', false),
        'endpoints' => [
            '/admin',
            '/wp-admin',
            '/phpmyadmin',
            '/.env',
        ],
        'ban_duration' => env('SECURITY_HONEYPOT_BAN_DURATION', 3600),
    ],
];
