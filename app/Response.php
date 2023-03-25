<?php

namespace App;

use App\Base\BaseObject;
use App\Exceptions\HttpException;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class Response
 * @package App
 *
 * @property int $statusCode
 *
 * @property-read array $headers
 */
final class Response extends BaseObject
{
    public array $data = [];
    public string $statusText = 'OK';
    public ?string $version = null;

    private array $headers = [];
    private int $statusCode = 200;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        if ($this->version === null) {
            if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') {
                $this->version = '1.0';
            } else {
                $this->version = '1.1';
            }
        }
    }

    /**
     * @return void
     */
    #[NoReturn] public function send(): void
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    /**
     * @param array $value
     * @return void
     */
    public function addData(array $value = []): void
    {
        if (!empty($value)) {
            $this->data = array_merge($value, $this->data);
        }
    }

    /**
     * @param string $name
     * @param mixed|null $default
     * @return string
     */
    public function getHeader(string $name, mixed $default = null): string
    {
        $name = strtolower($name);

        return $this->headers[$name] ?? $default;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setHeader(string $name, string $value = ''): void
    {
        $name = strtolower($name);
        $this->headers[$name] = (array)$value;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $value
     * @param string|null $text
     */
    public function setStatusCode(int $value, ?string $text = null): void
    {
        $this->statusCode = $value;

        if ($text === null) {
            $this->statusText = HttpException::HTTP_CODES[$this->statusCode] ?? '';
        } else {
            $this->statusText = $text;
        }
    }

    /**
     * @return void
     */
    private function sendHeaders(): void
    {
        if ($this->headers) {
            foreach ($this->getHeaders() as $name => $values) {
                $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
                foreach ($values as $value) {
                    header("$name: $value");
                }
            }
        }

        $statusCode = $this->getStatusCode();
        header("HTTP/{$this->version} {$statusCode} {$this->statusText}");
    }

    /**
     * @return void
     */
    #[NoReturn] private function sendContent(): void
    {
        exit(json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
    }
}
