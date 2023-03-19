<?php

namespace App\Base;

use App\Exceptions\InvalidCallException;
use App\Exceptions\UnknownPropertyException;

/**
 * Class BaseObject
 * @package App\Base
 */
class BaseObject
{
    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (!empty($config)) {
            configure($this, $config);
        }

        $this->init();
    }

    /**
     * Initializes the object with the given configuration.
     *
     * @return void
     */
    public function init(): void
    {
    }

    /**
     * @param $name
     * @return mixed
     * @throws InvalidCallException
     * @throws UnknownPropertyException
     */
    public function __get($name): mixed
    {
        $getter = 'get' . $name;

        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists($this, 'set' . $name)) {
            throw new InvalidCallException('Getting write-only property: ' . get_class($this) . '::' . $name);
        }

        throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
    }

    /**
     * @param $name
     * @param $value
     * @return void
     * @throws InvalidCallException
     * @throws UnknownPropertyException
     */
    public function __set($name, $value): void
    {
        $setter = 'set' . $name;

        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }
}
