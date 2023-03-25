<?php

namespace App\Base;

use App\App;
use App\Exceptions\InvalidCallException;
use App\Exceptions\UnknownMethodException;
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

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name): bool
    {
        $getter = 'get' . $name;

        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }

        return false;
    }

    /**
     * @param $name
     * @return void
     * @throws InvalidCallException
     */
    public function __unset($name): void
    {
        $setter = 'set' . $name;

        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Unsetting read-only property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * @param string $name
     * @param array $params
     * @return mixed
     * @throws UnknownMethodException
     */
    public function __call(string $name, array $params): mixed
    {
        throw new UnknownMethodException(App::$params['debug_mode'] ? 'Calling unknown method: ' . get_class($this) . "::$name()" : '');
    }
}
