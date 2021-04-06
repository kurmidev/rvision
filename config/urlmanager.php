<?php

return [
    'enablePrettyUrl' => true,
    'enableStrictParsing' => true,
    'showScriptName' => false,
    'rules' => [
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => [
                "v1/vi-access"
            ],
            'pluralize' => false,
            'extraPatterns' => [
                'OPTIONS login' => 'options',
                'POST login' => 'login',
                'OPTIONS login-token' => 'options',
                'POST login-token' => 'login-token',
                'OPTIONS login-hash' => 'options',
                'POST login-hash' => 'login-hash',
            ]
        ],
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => [
                "v1/operator",
                "v1/branch",
                "v1/area",
                "v1/society",
            ],
            'pluralize' => false,
        ],
        'GET,HEAD site' => 'site/index',
    ],
];
