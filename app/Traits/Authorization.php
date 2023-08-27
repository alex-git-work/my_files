<?php

namespace App\Traits;

use App\App;
use App\Exceptions\UnauthorizedException;
use App\Models\Role;

/**
 * Trait Authorization
 * @package App\Traits
 */
trait Authorization
{
    /**
     * @param bool $forAdmin
     * @return int
     * @throws UnauthorizedException
     */
    protected function authCheck(bool $forAdmin = false): int
    {
        $token = $this->getToken();

        if ($forAdmin) {
            $sql = 'SELECT * FROM users WHERE role_id = ' . Role::ROLE_ADMIN . ' AND token = :token';
        } else {
            $sql = 'SELECT * FROM users WHERE role_id = ' . Role::ROLE_USER . ' AND token = :token';
        }

        $user = App::$db->createCommand($sql, [':token' => $token])->query();
        $condition = strtotime($user['last_request']) <= strtotime('now - ' . App::$params['token_ttl'] . ' minutes');

        if ($user === false || $condition) {
            throw new UnauthorizedException();
        }

        App::$db->createCommand(
            'UPDATE users SET last_request = \'' . now() . '\' WHERE id = :id',
            [':id' => $user['id']]
        )->query();

        return (int)$user['id'];
    }

    /**
     * @return string
     * @throws UnauthorizedException
     */
    protected function getToken(): string
    {
        $token = App::$request->bearerToken;

        if ($token === null) {
            throw new UnauthorizedException();
        }

        return $token;
    }
}
