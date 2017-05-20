<?php

namespace App;

class Lang
{
    public static $defaultLanguage = "en";

    private static $currentLanguage = "en";

    // dictionaries per languages
    private static $dictionaries = [];

    public static function load($lang)
    {
        if (! isset(self::$dictionaries[$lang])) {
            $path = __DIR__."/../languages/$lang.php";
            if (file_exists($path)) {
                self::$dictionaries[$lang] = require $path;
                return true;
            }

            return false;
        }
    }

    /**
     * @param $originalKey
     * @param null $params
     * @return mixed
     */
    public static function get($keys, $params = null)
    {
        $originalKey = $keys;
        $keys = explode(".", $keys);
        $lang = $keys[0]; // either the language or the actual first key part

        if (! isset(self::$dictionaries[$lang])) {
            self::load($lang);
        }

        if (! isset(self::$dictionaries[$lang])) {
            // $lang is probably not a language identifier
            $lang = self::$currentLanguage;
        }

        $value = self::$dictionaries[$lang];

        foreach ($keys as $key) {
            if (isset($value[$key])) {
              $value = $value[$key];
            } elseif ($lang !== self::$defaultLanguage) {
              return self::get(self::$defaultLanguage.".".$originalKey, $params);
            }
        }

        if (! is_string($value)) {
            $value = $originalKey;
        }

        if (isset($params)) {
            foreach ($params as $key => $val) {
                $value = str_replace("{$key}", $val, $value);
            }
        }

        return $value;
    }
}
