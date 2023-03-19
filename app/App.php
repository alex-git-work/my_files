<?php

namespace App;

use Exception;

/**
 * Class App
 * @package App
 */
final class App
{
    public static Request $request;
    public static Response $response;

    private Router $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return void
     */
    public function run(): void
    {
        try {
            $this->router->dispatch();
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo $e->getMessage();
        }
    }
}
