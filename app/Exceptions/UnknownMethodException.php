<?php

namespace App\Exceptions;

/**
 * Class UnknownMethodException
 * @package App\Exceptions
 */
class UnknownMethodException extends HttpException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        parent::__construct($message ?: self::HTTP_CODES[400], 400);
    }
}
