<?php

namespace App\Exceptions;

use Exception;

/**
 * Class UnknownPropertyException
 * @package App\Exceptions
 */
class UnknownPropertyException extends Exception
{
    /**
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        parent::__construct($message, 400);
    }
}
