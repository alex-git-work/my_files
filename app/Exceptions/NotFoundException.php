<?php

namespace App\Exceptions;

/**
 * Class NotFoundException
 * @package App\Exceptions
 */
class NotFoundException extends HttpException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        parent::__construct($message ?: self::HTTP_CODES[404], 404);
    }
}
