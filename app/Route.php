<?php

namespace App;

use App\Base\BaseObject;
use App\Helpers\UrlHelper;

/**
 * Class Route
 * @package App
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

    public function run()
    {

    }
}
