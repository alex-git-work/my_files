<?php

namespace App\Helpers;

use App\Exceptions\InvalidConfigException;

/**
 * Class UrlHelper
 * @package App\Helpers
 */
class UrlHelper
{
    /**
     * @param string $string
     * @return string
     */
    public static function prepareUri(string $string): string
    {
        $newString = $string === '/' ? '/' : trim($string, '/');
        return str_starts_with($newString, '/') ? $newString : '/' . $newString;
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public static function getUri(): string
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (!is_string($requestUri)) {
            throw new InvalidConfigException('Unable to determine the request URI.');
        }

        return $requestUri;
    }
}
