<?php

namespace App\Base;

use App\App;
use App\Exceptions\InvalidCallException;
use App\Exceptions\InvalidConfigException;
use App\Exceptions\UnknownPropertyException;
use Exception;
use Throwable;

/**
 * Class Model
 * @package App\Base
 *
 * @property array $attributes Attribute values (name => value).
 * @property array $oldAttributes Old attribute values (name => value).
 * @property bool $isNewRecord Whether the record is new
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
     * {@inheritdoc}
     */
    public function init(): void
    {
        $this->isNewRecord = $this->getAttribute('id') === null;
    }

    /**
     * @param string $name property name
     * @return mixed property value
     * @throws InvalidCallException
     * @throws UnknownPropertyException
     */
    public function __get(string $name): mixed
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
    public function __set(string $name, $value): void
    {
        if ($this->hasAttribute($name)) {
            $this->_attributes[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        try {
            return $this->__get($name) !== null;
        } catch (Exception|Throwable $et) {
            return false;
        }
    }

    /**
     * @param string $name
     * @return void
     * @throws InvalidCallException
     */
    public function __unset(string $name): void
    {
        if ($this->hasAttribute($name)) {
            unset($this->_attributes[$name]);
        } else {
            parent::__unset($name);
        }
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return App::$db->schema->getColumns(self::tableName());
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getAttribute(string $name): mixed
    {
        return $this->_attributes[$name] ?? null;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     * @throws InvalidConfigException
     */
    public function setAttribute(string $name, mixed $value): void
    {
        if ($this->hasAttribute($name)) {
            $this->_attributes[$name] = $value;
        } else {
            throw new InvalidConfigException(get_class($this) . ' has no attribute named "' . $name . '".');
        }
    }

    /**
     * @param array $names
     * @param array $except
     * @return array
     */
    public function getAttributes(array $names = [], array $except = []): array
    {
        $values = [];
        if (!$names) {
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
     * @return array
     */
    public function getOldAttributes(): array
    {
        return $this->_oldAttributes === null ? [] : $this->_oldAttributes;
    }

    /**
     * @param array|null $values
     * @return void
     */
    public function setOldAttributes(?array $values): void
    {
        $this->_oldAttributes = $values;
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
     * @param bool $value
     * @return void
     */
    public function setIsNewRecord(bool $value): void
    {
        $this->_oldAttributes = $value ? null : $this->_attributes;
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if ($this->isNewRecord) {
            $id = self::create($this->attributes(), array_values($this->attributes));

            if ($id !== false) {
                $this->isNewRecord = false;
                $this->_oldAttributes = $this->attributes;

                return true;
            }

            return false;
        }

        return self::update($this->attributes(), array_values($this->attributes));
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $this->setOldAttributes(null);

        return self::destroy($this->getAttribute('id'));
    }

    /**
     * @return bool
     */
    public function refresh(): bool
    {
        $record = static::findOne(App::$db->lastInsertID);

        return $this->refreshInternal($record);
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

    /**
     * @param Model|null $record
     * @return bool
     */
    protected function refreshInternal(?Model $record): bool
    {
        if ($record === null) {
            return false;
        }
        foreach ($this->attributes() as $name) {
            $this->_attributes[$name] = $record->_attributes[$name] ?? null;
        }
        $this->_oldAttributes = $record->_oldAttributes;

        return true;
    }
}
