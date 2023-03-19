<?php

namespace App\Helpers;

/**
 * Class ArrayHelper
 * @package App\Helpers
 */
class ArrayHelper
{
    /**
     * @param array $array
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public static function get(array $array, string $key, mixed $default = null): mixed
    {
        if (!$array || !$key) {
            return $default;
        }

        $parts = explode('.', $key);
        $partsCount = count($parts);
        $level = [];

        for ($i = 0; $i < $partsCount; $i++) {
            if (!$level) {
                $level[$i] = $array[$parts[0]] ?? $default;
            } else {
                $level[$i] = $level[$i - 1][$parts[$i]] ?? $default;
            }
        }

        return $level[$partsCount - 1];
    }
}
