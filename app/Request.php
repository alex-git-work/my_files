<?php

namespace App;

use App\Base\BaseObject;

/**
 * Class Request
 * @package App
 *
 * @property array|null $queryParams
 * @property array|null $bodyParams
 * @property array|null $parsedBody
 * @property string|null $rawBody
 *
 * @property-read bool $isGet
 * @property-read bool $isPost
 * @property-read string $method
 * @property-read string|null $email
 * @property-read string|null $password
 * @property-read string|null $bearerToken
 */
final class Request extends BaseObject
{
    private ?array $bodyParams;
    private ?array $parsedBody;
    private ?array $queryParams;
    private ?string $rawBody;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        if ($this->getRawBody() !== null) {
            $this->setRawBody($this->rawBody);
            $this->setBodyParams(null);
            $this->setParsedBody($this->rawBody);
        } else {
            $this->setRawBody(null);
            $this->setBodyParams($_POST);
            $this->setParsedBody('');
        }

        $this->setQueryParams($_GET ?: null);

        parent::init();
    }

    /**
     * @return bool
     */
    public function getIsGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * @return bool
     */
    public function getIsPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * @param null $name
     * @param null $defaultValue
     * @return array|string|null
     */
    public function get($name = null, $defaultValue = null): array|string|null
    {
        if ($name === null) {
            return $this->getQueryParams();
        }

        return $this->getQueryParam($name, $defaultValue);
    }

    /**
     * @param null $name
     * @param null $defaultValue
     * @return array|string|null
     */
    public function post($name = null, $defaultValue = null): array|string|null
    {
        if ($name === null) {
            return $this->getBodyParams();
        }

        return $this->getBodyParam($name, $defaultValue);
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }

        return 'GET';
    }

    /**
     * @param $name
     * @param null $defaultValue
     * @return array|string|null
     */
    public function getQueryParam($name, $defaultValue = null): array|string|null
    {
        $params = $this->getQueryParams();

        return $params[$name] ?? $defaultValue;
    }

    /**
     * @return array|null
     */
    public function getQueryParams(): ?array
    {
        if ($this->queryParams === null) {
            if ($this->getMethod() === 'GET') {
                $this->queryParams = $_GET ?: null;
            }
        }

        return $this->queryParams;
    }

    /**
     * @param array|null $values
     * @return void
     */
    public function setQueryParams(?array $values): void
    {
        $this->queryParams = $values;
    }

    /**
     * @param $name
     * @param $defaultValue
     * @return array|string|null
     */
    public function getBodyParam($name, $defaultValue = null): array|string|null
    {
        $params = $this->getBodyParams();

        return $params[$name] ?? $defaultValue;
    }

    /**
     * @return array|null
     */
    public function getBodyParams(): ?array
    {
        if ($this->bodyParams === null) {
            if ($this->getMethod() === 'POST') {
                $this->bodyParams = $_POST ?: null;
            }
        }

        return $this->bodyParams;
    }

    /**
     * @param array|null $values
     * @return void
     */
    public function setBodyParams(?array $values): void
    {
        $this->bodyParams = $values;
    }

    /**
     * @return array|null
     */
    public function getParsedBody(): ?array
    {
        return $this->parsedBody;
    }

    /**
     * @param string $value
     * @return void
     */
    public function setParsedBody(string $value): void
    {
        $this->parsedBody = json_decode($value, true) ?: null;
    }

    /**
     * @return string|null
     */
    public function getRawBody(): ?string
    {
        if (!isset($this->rawBody)) {
            $this->rawBody = file_get_contents('php://input') ?: null;
        }

        return $this->rawBody;
    }

    /**
     * @param string|null $rawBody
     * @return void
     */
    public function setRawBody(?string $rawBody): void
    {
        $this->rawBody = $rawBody;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $_SERVER['PHP_AUTH_USER'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $_SERVER['PHP_AUTH_PW'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getBearerToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

        if (!$header) {
            return null;
        }

        $token = null;

        if (str_starts_with($_SERVER['HTTP_AUTHORIZATION'], 'Bearer ')) {
            $token = substr($header, 7);
        }

        return $token;
    }
}
