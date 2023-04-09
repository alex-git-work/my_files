<?php

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * Class DbException
 * @package App\Exceptions
 */
class DbException extends Exception
{
    /**
     * @var array
     */
    public array $errorInfo = [];

    /**
     * @param string $message
     * @param array $errorInfo
     * @param string $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message, array $errorInfo = [], string $code = '', Throwable $previous = null)
    {
        $this->errorInfo = $errorInfo;
        $this->code = $code;

        parent::__construct($message, 0, $previous);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Database Exception';
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return parent::__toString() . PHP_EOL . 'Additional Information:' . PHP_EOL . print_r($this->errorInfo, true);
    }
}
