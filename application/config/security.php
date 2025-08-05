<?php
return [
    'ip_whitelist' => [
        '127.0.0.1',
        '::1',
    ],
    'oauth_security' => [
        'block_high_risk_threshold' => 90,
        'block_proxy_with_risk_threshold' => 60,
        'max_attempts_per_email' => 5,
    ],
];
