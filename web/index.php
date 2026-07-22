<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// Check if there's a route parameter in the request
$route = isset($_GET['r']) ? $_GET['r'] : null;

// If no route is specified, check session and redirect
if (empty($route)) {
    session_start();

    if (isset($_SESSION['user_array']) && !empty($_SESSION['user_array'])) {
        // Session is active, redirect to dashboard
        header('Location: index.php?r=inventory/dashboard');
        exit();
    } else {
        // No session, redirect to login
        header('Location: index.php?r=site/login');
        exit();
    }
}

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();