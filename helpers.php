<?php

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
