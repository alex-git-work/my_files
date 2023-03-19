<?php

namespace App\Exceptions;

/**
 * Class ClassNotFoundException
 * @package App\Exceptions
 */
class ClassNotFoundException extends HttpException
{
    /**
     * @param string $class
     */
    public function __construct(string $class)
    {
        parent::__construct(
            self::HTTP_CODES[500] . ': Class Not Found - ' . $class,
            500
        );
    }
}
