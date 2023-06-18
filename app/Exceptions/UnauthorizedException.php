<?php

namespace App\Exceptions;

/**
 * Class UnauthorizedException
 * @package App\Exceptions
 */
class UnauthorizedException extends HttpException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        parent::__construct($message ?: self::HTTP_CODES[401], 401);
    }
}
