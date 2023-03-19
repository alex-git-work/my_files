<?php

namespace App\Exceptions;

use Exception;

/**
 * Class InvalidCallException
 * @package App\Exceptions
 */
class InvalidCallException extends Exception
{
    /**
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        parent::__construct($message, 400);
    }
}
