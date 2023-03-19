<?php

namespace App\Helpers;

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
}
