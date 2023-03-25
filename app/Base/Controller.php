<?php

namespace App\Base;

use App\App;
use App\Request;
use App\Response;

/**
 * Class Controller
 * @package App\Base
 */
class Controller extends BaseObject
{
    public Request $request;
    public Response $response;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        App::$request = $this->request = new Request();
        App::$response = $this->response = new Response();

        parent::init();
    }

    /**
     * @param array $data
     * @return Response
     */
    protected function asJson(array $data = []): Response
    {
        $this->response->data = $data;
        return $this->response;
    }
}
