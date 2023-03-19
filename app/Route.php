<?php

namespace App;

use App\Base\BaseObject;
use App\Exceptions\ClassNotFoundException;
use App\Helpers\StringHelper;
use App\Helpers\UrlHelper;

/**
 * Class Route
 * @package App
 *
 * @property-read string $method
 */
final class Route extends BaseObject
{
    private string $method;
    private string $uri;
    private string $controller;
    private string $action;

    /**
     * @param string $method
     * @param string $uri
     * @param string $controller
     * @param string $action
     * @param array $config
     */
    public function __construct(string $method, string $uri, string $controller, string $action, array $config = [])
    {
        $this->method = $method;
        $this->uri = UrlHelper::prepareUri($uri);
        $this->controller = $controller;
        $this->action = $action;

        parent::__construct($config);
    }

    /**
     * @param string $value
     * @return bool
     */
    public function match(string $value): bool
    {
        if ($this->uri === $value) {
            return true;
        }

        if (str_contains($this->uri, '*')) {
            $pattern = StringHelper::preparePatternFromString($this->uri);

            if (preg_match($pattern, $value) !== false && preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $uri
     * @return mixed
     * @throws ClassNotFoundException
     */
    public function run(string $uri): mixed
    {
        return $this->call($uri);
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $uri
     * @return mixed
     * @throws ClassNotFoundException
     */
    private function call(string $uri): mixed
    {
        if (!class_exists($this->controller)) {
            throw new ClassNotFoundException($this->controller);
        }

        $controller = new $this->controller;
        $action = $this->action;
        $arguments = UrlHelper::getUriParams($uri, $this->uri);

        return $controller->{$action}(...$arguments);
    }
}
