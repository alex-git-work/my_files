<?php

namespace App\Controllers;

use App\Base\Controller;
use App\Response;

/**
 * Class MainController
 * @package App\Controllers
 */
class MainController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return $this->asJson([
            'message' => 'Hello World!'
        ]);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function user(int $id): Response
    {
        return $this->asJson([
            'message' => 'You are trying to find user with id: ' . $id
        ]);
    }

    /**
     * POST-requests only
     * @return Response
     */
    public function update(): Response
    {
        return $this->asJson([
            'message' => 'success'
        ]);
    }
}
