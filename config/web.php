<?php

$params = require __DIR__ . '/params.php';

$conf = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'rvision_ptrhu11Wu6Zj8wBirbZx',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
// 'useFileTransport' to false and configure a transport
// for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        "urlManager" => require_once __DIR__ . '/urlmanager.php',
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                $responseData = $response->data;
                if ($response->format == 'html') {
                    return $response;
                }
                if ($response->format == 'raw') {
                    return $response;
                }


                if (is_string($responseData) && json_decode($responseData)) {
                    $responseData = json_decode($responseData, true);
                }
                if (isset($responseData['message'])) {
                    $responseData['message'] = json_decode($responseData['message'], true);
                }
                if ($response->statusCode >= 200 && $response->statusCode <= 299) {
                    $response->data = [
                        'success' => true,
                        'status' => $response->statusCode,
                        'data' => $responseData,
                    ];
                } else {
                    $response->data = [
                        'success' => false,
                        'status' => $response->statusCode,
                        'data' => $responseData,
                    ];
                }
            }
        ],
        'user' => [
            'identityClass' => 'app\models\ViAccess',
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
// configuration adjustments for 'dev' environment
    $conf['bootstrap'][] = 'debug';
    $conf['modules']['debug'] = [
        'class' => 'yii\debug\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
//'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $conf['bootstrap'][] = 'gii';
    $conf['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'mymodel' => [// generator name
                'class' => \app\generators\model\Generator::class, // generator class
            ],
            'rest' => [//
                'class' => \app\generators\rest\Generator::class,
            ],
        ]
// uncomment the following to add your IP if you are not connecting from localhost.
//'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return loadConfig(".local", __DIR__, $conf);
