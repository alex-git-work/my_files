<?php

namespace App\Controllers;

use App\App;
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
        $sql = 'SELECT COUNT(*) AS count FROM users';
        $qty = App::$db->createCommand($sql)->query();

        return $this->asJson([
            'message' => 'Hello World!',
            'users' => $qty,
        ]);
    }
}
