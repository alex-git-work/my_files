<?php

namespace App\Exceptions;

/**
 * Class InvalidConfigException
 * @package App\Exceptions
 */
class InvalidConfigException extends HttpException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(string $message = '')
    {
        parent::__construct($message ?: self::HTTP_CODES[500], 500);
    }
}
