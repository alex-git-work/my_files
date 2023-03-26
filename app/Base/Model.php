<?php

namespace App\Base;

use App\Exceptions\InvalidCallException;
use App\Exceptions\UnknownPropertyException;
use Exception;
use Throwable;

/**
 * Class Model
 * @package App\Base
 *
 * @property array $attributes Attribute values (name => value).
 *
 * @property-read bool $isNewRecord Whether the record is new
 */
class Model extends Query
{
    private array $_attributes = [];
    private ?array $_oldAttributes = null;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->loadDefaultValues();
        parent::__construct($config);
    }

    /**
     * @param string $name property name
     * @return mixed property value
     * @throws InvalidCallException
     * @throws UnknownPropertyException
     */
    public function __get($name): mixed
    {
        if (isset($this->_attributes[$name]) || array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        }

        return parent::__get($name);
    }

    /**
     * @param string $name property name
     * @param mixed $value property value
     * @return void
     * @throws InvalidCallException
     * @throws UnknownPropertyException
     */
    public function __set($name, $value): void
    {
        if ($this->hasAttribute($name)) {
            $this->_attributes[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name): bool
    {
        try {
            return $this->__get($name) !== null;
        } catch (Exception|Throwable $et) {
            return false;
        }
    }

    /**
     * @param string $name the property name
     * @throws InvalidCallException
     */
    public function __unset($name): void
    {
        if ($this->hasAttribute($name)) {
            unset($this->_attributes[$name]);
        } else {
            parent::__unset($name);
        }
    }

    /**
     * @return array all public non-static properties of the class
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * @param $names
     * @param array $except
     * @return array
     */
    public function getAttributes($names = null, array $except = []): array
    {
        $values = [];
        if ($names === null) {
            $names = $this->attributes();
        }
        foreach ($names as $name) {
            $values[$name] = $this->{$name};
        }
        foreach ($except as $name) {
            unset($values[$name]);
        }

        return $values;
    }

    /**
     * @param $values
     * @return void
     */
    public function setAttributes($values): void
    {
        if (is_array($values)) {
            $attributes = array_flip($this->attributes());
            foreach ($values as $name => $value) {
                if (isset($attributes[$name])) {
                    $this->{$name} = $value;
                }
            }
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return isset($this->_attributes[$name]) || in_array($name, $this->attributes(), true);
    }

    /**
     * @return bool
     */
    public function getIsNewRecord(): bool
    {
        return $this->_oldAttributes === null;
    }

    /**
     * @param $value
     * @return void
     */
    protected function loadDefaultValues($value = null): void
    {
        foreach ($this->attributes() as $attribute) {
            $this->_attributes[$attribute] = $value;
        }
    }
}
