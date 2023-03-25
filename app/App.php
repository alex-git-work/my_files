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
    public static array $params;

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
        } catch (Exception $e) {
            $response = $this->getErrorResponse($e);
        } finally {
            $this->sendResponse($response);
        }
    }

    /**
     * @return void
     */
    private function init(): void
    {
        self::$params = include CONFIGS . 'main.php';
    }

    /**
     * @param Exception $e
     * @return Response
     */
    private function getErrorResponse(Exception $e): Response
    {
        $response = new Response();
        $response->setStatusCode((int)$e->getCode(), $e->getMessage());
        $response->data = [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
        ];

        return $response;
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
