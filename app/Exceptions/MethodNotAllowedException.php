<?php

namespace App\Exceptions;

/**
 * Class MethodNotAllowedException
 * @package App\Exceptions
 */
class MethodNotAllowedException extends HttpException
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct(self::HTTP_CODES[405] . ': ' . $_SERVER['REQUEST_METHOD'], 405);
    }
}
