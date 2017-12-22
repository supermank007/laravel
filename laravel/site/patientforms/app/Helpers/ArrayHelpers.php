<?php

if (!function_exists('map')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function map(callable $fn, $iterable) {
        $result = array();

        foreach ($iterable as $item) {
            $result[] = $fn($item);
        }

        return $result;
    }

}