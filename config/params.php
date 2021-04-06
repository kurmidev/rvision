<?php

return [
    'login' => [
        'creation_otp_required' => true,
        'force_change_password_onfirst_login' => true,
        'masterKey' => '$2y$13$6w7inljYIvTPQc9r.KQuq.A46zeP1gtQAcunc3IHN/nwNsk0fNI5u',
        'customer_app_token' => 'xqibzknznwb29de15s44',
        'admin_app_token' => 'ivt5y01iwq75sysm7ol7',
        'idle_time' => 0,
    ],
    'page_size' => ['min' => 0, 'max' => 5000, 'default' => 1000], //min 0 all
    'jwtSecretCode' => 'rvison',
    'masterResetToken' => hash('sha256', '_masterResetToken_'),
    'cache' => [
        'enabled' => true,
        'query_cache_duration' => 5 * 60,
        'queryParam' => 'nocache',
        'dependency_type' => 'redis'//file|redis
    ],
];
