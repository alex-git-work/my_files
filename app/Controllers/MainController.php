<?php

namespace App\Controllers;

use App\Base\Controller;

/**
 * Class MainController
 * @package App\Controllers
 */
class MainController extends Controller
{
    /**
     * @return void
     */
    public function index(): void
    {
        echo 'Hello World!';
    }

    /**
     * @param int $id
     * @return void
     */
    public function user(int $id): void
    {
        echo 'You are trying to find user with id: ' . $id;
    }

    /**
     * POST-requests only
     * @return void
     */
    public function update(): void
    {
        echo 'success';
    }
}
