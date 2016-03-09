<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config/routes.php';

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../src/config');
$dotenv->load();

define('CAIXA_INI_FILE', __DIR__ . '/../src/config/config.ini');

$config = new Piano\Config\Ini(CAIXA_INI_FILE);
$configIni = $config->get();

if (getenv('APPLICATION_ENV') == 'development') {
    ini_set('display_errors', 1);
    error_reporting(-1);
}

$layoutPerModule = [
    'base' => [
        'application',
    ],
    'login' => [
        'authentication',
    ]
];

$router = new Piano\Router();
$router->setRoutes($routes)
    ->enableSearchEngineFriendly($configIni['enableFriendlyUrl']);

$app = new Piano\Application($config, $router);
$app->registerModulesLayout($layoutPerModule);
$app->run();
