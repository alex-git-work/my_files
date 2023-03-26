<?php

namespace App\Controllers;

use App\App;
use App\Base\Controller;
use App\Exceptions\NotFoundException;
use App\Models\User;
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
        $qty = App::$db->createCommand($sql);

        return $this->asJson([
            'message' => 'Hello World!',
            'users' => $qty,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundException
     */
    public function user(int $id): Response
    {
        $user = User::findOne($id);

        if (!$user) {
            throw new NotFoundException('User not found');
        }

        return $this->asJson(['user' => $user]);
    }

    /**
     * @return Response
     */
    public function users(): Response
    {
        $users = User::findAll(['id' => [1, 3]]);
        return $this->asJson(['users' => $users]);
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
