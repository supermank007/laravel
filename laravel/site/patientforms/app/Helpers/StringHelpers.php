<?php

if (!function_exists('starts_with')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function starts_with($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}

if (!function_exists('ends_with')) {
    function ends_with($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
}

if (!function_exists('plural')) {
    function plural($word, $plural, $count) {
        return ($count != 1) ? $plural : $word;
    }
}

if (!function_exists('count_plural')) {
    function count_plural($word, $plural, $array) {
        return count($array) . ' ' . plural($word, $plural, count($array));
    }
}