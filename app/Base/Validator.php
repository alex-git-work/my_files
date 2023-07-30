<?php

namespace App\Base;

use App\App;

/**
 * Class Validator
 * @package App\Base
 *
 * @property-read array $validatedData
 * @property-read array $errors
 * @property-read string $firstError
 */
class Validator extends BaseObject
{
    public array $except = [];
    public array $keys = [];
    public bool $isAdminSection = false;

    protected array $requireKeys = [];
    protected array $rawData;
    protected ?int $entityId;
    protected array $cleanData = [];
    protected array $errors = [];
    protected bool $result = false;

    /**
     * @param array $data
     * @param int|null $id
     * @param array $config
     */
    public function __construct(array $data, int $id = null, array $config = [])
    {
        $this->rawData = $data;
        $this->entityId = $id;
        parent::__construct($config);
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        return true;
    }

    /**
     * @param $attribute
     * @param $message
     * @return void
     */
    public function addError($attribute, $message): void
    {
        $this->errors[] = [$attribute => $message];
    }

    /**
     * @return array
     */
    public function getValidatedData(): array
    {
        return $this->cleanData;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getFirstError(): string
    {
        if (empty($this->errors)) {
            return '';
        }

        return array_values(reset($this->errors))[0];
    }

    /**
     * @param array $data
     * @return void
     */
    public function setRequiredKeys(array $data): void
    {
        $this->requireKeys = $data;
    }

    /**
     * @param array $except
     * @return void
     */
    protected function addCleanData(array $except = []): void
    {
        foreach ($this->keys as $key) {
            if (in_array($key, $except)) {
                continue;
            }

            $cleanValue = $this->purify($this->rawData[$key]);

            if (empty($cleanValue)) {
                $this->addError($key, 'Attribute [' . $key . '] is incorrect');
            } else {
                $this->cleanData[$key] = $cleanValue;
            }
        }
    }

    /**
     * @param string $value
     * @return string
     */
    protected function purify(string $value): string
    {
        $value = trim(strip_tags($value));

        return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
    }

    /**
     * @param array $except
     * @return void
     */
    protected function required(array $except = []): void
    {
        foreach ($this->requireKeys as $key) {
            if (!in_array($key, $this->keys)) {
                $this->addError($key, 'Attribute [' . $key . '] is required');
            }
        }

        foreach ($this->keys as $key) {
            if (in_array($key, $except)) {
                continue;
            }
            if (empty($this->rawData[$key])) {
                $this->addError($key, 'Attribute [' . $key . '] is required');
            }
        }
    }

    /**
     * @param string $value
     * @return string|false
     */
    protected function int(string $value): string|false
    {
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    /**
     * @param string $value
     * @return string|false
     */
    protected function float(string $value): string|false
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    /**
     * @param string $value
     * @return string|false
     */
    protected function boolean(string $value): string|false
    {
        return filter_var($value, FILTER_VALIDATE_BOOL);
    }

    /**
     * @param string $value
     * @return string|false
     */
    protected function email(string $value): string|false
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @return void
     */
    protected function end(): void
    {
        if (!empty($this->errors)) {
            $this->cleanData = [];
            App::$response->setStatusCode(400);
        } else {
            $this->result = true;
        }
    }
}
