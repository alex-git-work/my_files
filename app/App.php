<?php

namespace App;

use App\Exceptions\DbException;
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
    public static Connection $db;
    public static array $params;

    private Router $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;

        try {
            $this->init();
        } catch (Exception $e) {
            $response = $this->getErrorResponse($e);
            $this->sendResponse($response);
        }
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

            if (self::$params['show_queries']) {
                $response->addData(['queries' => self::$db->queryLog]);
            }
        } catch (Exception $e) {
            $response = $this->getErrorResponse($e);
        } finally {
            $this->sendResponse($response);
        }
    }

    /**
     * @return void
     * @throws DbException
     */
    private function init(): void
    {
        $config = include CONFIGS . 'db.php';
        self::$db = new Connection($config);
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
