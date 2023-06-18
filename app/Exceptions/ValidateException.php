<?php

namespace App\Exceptions;

/**
 * Class ValidateException
 * @package App\Exceptions
 */
class ValidateException extends HttpException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        parent::__construct($message ?: self::HTTP_CODES[400], 400);
    }
}
