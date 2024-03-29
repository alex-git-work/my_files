<?php

namespace App\Helpers;

use App\App;
use Exception;

/**
 * Class StringHelper
 * @package App\Helpers
 */
class StringHelper
{
    /**
     * @param string $string
     * @return string
     */
    public static function preparePatternFromString(string $string): string
    {
        return '<^' . str_replace('*', '[\w]+', $string) . '>';
    }

    /**
     * @param $path
     * @param string $suffix
     * @return string
     */
    public static function basename($path, string $suffix = ''): string
    {
        $len = mb_strlen($suffix);
        if ($len > 0 && mb_substr($path, -$len) === $suffix) {
            $path = mb_substr($path, 0, -$len);
        }

        $path = rtrim(str_replace('\\', '/', $path), '/');
        $pos = mb_strrpos($path, '/');
        if ($pos !== false) {
            return mb_substr($path, $pos + 1);
        }

        return $path;
    }

    /**
     * @param $name
     * @param string $separator
     * @param bool $strict
     * @return string
     */
    public static function camel2id($name, string $separator = '-', bool $strict = false): string
    {
        $regex = $strict ? '/\p{Lu}/u' : '/(?<!\p{Lu})\p{Lu}/u';
        if ($separator === '_') {
            return mb_strtolower(trim(preg_replace($regex, '_\0', $name), '_'), App::$params['encoding']);
        }

        return mb_strtolower(trim(str_replace('_', $separator, preg_replace($regex, $separator . '\0', $name)), $separator), App::$params['encoding']);
    }

    /**
     * @param int $length
     * @return string
     * @throws Exception
     */
    public static function generateRandomString(int $length): string
    {
        $chars = '0123456789abcdef';
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $start = random_int(1, strlen($chars) - 1);
            $string .= substr($chars, $start, 1);
        }

        return $string;
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function generateToken(): string
    {
        return self::generateRandomString(32);
    }
}
