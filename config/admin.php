<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],


    'components' => [
        // Custom components
        'Component' => [
            'class' => 'app\components\Components',
        ],
        'Permissions' => [
            'class' => 'app\components\Permissions',
        ],

        // Request component with CSRF and cookie validation
        'request' => [
            'cookieValidationKey' => 'hqPWLHPy2rejc6dLPMUb4GRXIx9p9qWW',
            // Uncomment below to enable JSON input parsing
            // 'parsers' => [
            //     'application/json' => 'yii\web\JsonParser',
            // ],
        ],


        // Cache component
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        // User authentication component
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            //'enableSession' => true,
            'loginUrl' => ['site/login'],
            'authTimeout' => 3600,
            'identityCookie' => [
                'name' => 'hsms_1',
                'path' => 'https://creativegarage.org/hsms/web/'  // correct path for the basictest1 app.
            ]
            //'as afterLogin' => 'common\behaviors\LoginTimestampBehavior',

        ],
        'session' => [
            'class' => 'yii\web\Session',
            'timeout' => 1440,
            'name' => 'advanced-session',
            'cookieParams' => ['httpOnly' => true, 'sameSite' => 'Lax'],
        ],

        // Error handler
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        // Mailer (sends all emails to file in development)
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true, // Set to false to send real emails in production
        ],

        // Logging component
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],

        // Database connection (check db.php for configuration)
        'db' => $db,

        // Uncomment the following to enable Pretty URLs (SEO friendly URLs)
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // Add custom routing rules here
            ],
        ],
        */
    ],

    // Application-wide parameters (loaded from params.php)
    'params' => $params,
];

if (YII_ENV_DEV) {
    // Adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // Uncomment the following to limit access to debug from specific IPs
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // Uncomment the following to limit access to Gii from specific IPs
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
