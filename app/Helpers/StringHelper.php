<?php

namespace App\Helpers;

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
        return '<' . str_replace('*', '[\w]+', $string) . '>';
    }
}
