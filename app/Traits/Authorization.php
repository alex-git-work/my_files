<?php

namespace App\Traits;

use App\App;
use App\Exceptions\UnauthorizedException;

/**
 * Trait Authorization
 * @package App\Traits
 */
trait Authorization
{
    /**
     * @return int
     * @throws UnauthorizedException
     */
    protected function authCheck(): int
    {
        $token = $this->getToken();

        $sql = 'SELECT * FROM users WHERE token = :token';

        $user = App::$db->createCommand($sql, [':token' => $token])->query();

        if ($user === false || strtotime($user['last_request']) <= strtotime('now - ' . App::$params['token_ttl'] . ' minutes')) {
            throw new UnauthorizedException();
        }

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
