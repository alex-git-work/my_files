<?php

/**
 * @var array $allRoutes
 */

declare(strict_types=1);

use App\App;
use App\Router;

require_once __DIR__ . '/bootstrap.php';

if (params('debug_mode', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

$router = new Router();

foreach ($allRoutes as $method => $routes) {
    foreach ($routes as $uri => [$controller, $action]) {
        $router->registerRoute($method, $uri, $controller, $action);
    }
}

$app = new App();
