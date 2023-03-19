<?php

use App\Helpers\ArrayHelper;

if (!function_exists('configure')) {
    /**
     * Configures an object with the property values.
     *
     * @param object $object
     * @param array $properties
     * @return object
     */
    function configure(object $object, array $properties): object
    {
        foreach ($properties as $name => $value) {
            $object->{$name} = $value;
        }

        return $object;
    }
}

if (!function_exists('params')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    function params(string $key, mixed $default = null): mixed
    {
        $params = include ENV . (IS_DEV_SERVER ? 'params-local.php' : 'params.php');

        return ArrayHelper::get($params, $key, $default);
    }
}
