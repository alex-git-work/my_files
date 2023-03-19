<?php

namespace App;

use App\Base\BaseObject;
use App\Exceptions\InvalidConfigException;
use App\Exceptions\MethodNotAllowedException;
use App\Exceptions\NotFoundException;
use App\Helpers\UrlHelper;

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
    private string $uri;
    private string $method;

    /**
     * @return void
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        $this->uri = UrlHelper::getUri();
        $this->method = $_SERVER['REQUEST_METHOD'];

        parent::init();
    }

    /**
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     */
    public function dispatch()
    {
        $matchedRoutes = [];

        foreach ($this->routes as $route) {
            if ($route->match($this->uri)) {
                $matchedRoutes[] = $route;
            }
        }

        if (empty($matchedRoutes)) {
            throw new NotFoundException();
        }

        foreach ($matchedRoutes as $route) {
            if ($this->method === $route->method) {
                return $route->run($this->uri);
            }
        }

        throw new MethodNotAllowedException();
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
