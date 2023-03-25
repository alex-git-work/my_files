<?php

namespace App;

use Exception;
use JetBrains\PhpStorm\NoReturn;

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
            $response = $this->router->dispatch();
            $response->setStatusCode(200);
            $response->addData(['code' => 200]);
            $this->sendResponse($response);
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo $e->getMessage();
        }
    }

    /**
     * @param Response $response
     * @return void
     */
    #[NoReturn] private function sendResponse(Response $response): void
    {
        $response->setHeader('Cache-Control', 'no-cache, must-revalidate');
        $response->setHeader('Content-Type', 'application/json; charset=utf-8');
        $response->send();
    }
}
