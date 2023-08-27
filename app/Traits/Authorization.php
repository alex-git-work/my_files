<?php

namespace App\Traits;

use App\App;
use App\Exceptions\UnauthorizedException;
use App\Models\User;

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

        $sql = 'SELECT * FROM users WHERE token = :token';

        $user = App::$db->createCommand($sql, [':token' => $token])->query();

        if ($user === false) {
            throw new UnauthorizedException();
        }

        $tokenExpired = strtotime($user['last_request']) <= strtotime('now - ' . App::$params['token_ttl'] . ' minutes');

        if ($tokenExpired) {
            throw new UnauthorizedException();
        }

        if ($forAdmin && ($user['role_id'] !== User::ROLE_ADMIN)) {
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
