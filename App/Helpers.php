<?php

namespace App;

class Helpers
{
    public static function arrayGetKey($array, $key, $defaultValue = null)
    {
        return isset($array[$key]) ? $array[$key] : $defaultValue;
    }
}

