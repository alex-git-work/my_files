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
     * @param int $code
     */
    public function __construct(string $message = '', int $code = 400)
    {
        parent::__construct($message ?: self::HTTP_CODES[$code], $code);
    }
}
