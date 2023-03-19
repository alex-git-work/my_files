<?php

namespace App;

use App\Base\BaseObject;

/**
 * Class Router
 * @package App
 */
final class Router extends BaseObject
{
    /**
     * @var Route[]
     */
    private array $routes = [];

    public function dispatch()
    {

    }

    /**
     * @param string $method
     * @param string $uri
     * @param string $controller
     * @param string $action
     * @return void
     */
    public function registerRoute(string $method, string $uri, string $controller, string $action): void
    {
        $this->routes[] = new Route($method, $uri, $controller, $action);
    }
}
