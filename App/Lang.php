<?php

namespace App;

/**
 * Class Lang
 * Provide localization capabilities, but also allow to store strings in dictionaries instead of the App's code (ie: messages, form labels, ..).
 * Dictionaries are found in their own file in the languages folder. The file must be name after the language identifier (en, fr, de, ...).
 * The file must return an associative array of (potentially) nested string keys and values.
 * @package App
 */
class Lang
{
    public static $defaultLanguage = "en";

    public static $currentLanguage = "en";

    /**
     * dictionaries per language
     */
    private static $dictionaries = [];

    /**
     * @param string $lang The language identifier ie: en, fr, de, ...
     * @return bool Returns true when the language has been loaded during this function call, false otherwise.
     */
    public static function load($lang)
    {
        if (! isset(self::$dictionaries[$lang])) {
            $path = __DIR__."/../languages/$lang.php";
            if (file_exists($path)) {
                self::$dictionaries[$lang] = require $path;
                return true;
            }
        }

        return false;
    }

    /**
     * Get a language string.
     * When the string is nested in "sub folders", the key can use the "dot notation". Ie: "messages.success.user.created".
     * The first part of the key may be any of the language name, the string will be retrieved in that language.
     * Otherwise it is retrieved in the current language.
     * If the key(s) do not lead to a string, the keys string is returned instead.
     * A set of keys/values to be replaced in the string can be passed as the replacements argument.
     * @param string $keys
     * @param array $replacements
     * @return string
     */
    public static function get($keys, $replacements = null)
    {
        $originalKey = $keys;
        $keys = explode(".", $keys);
        $lang = $keys[0]; // either the language or the actual first key part

        if (! isset(self::$dictionaries[$lang])) {
            self::load($lang);
        }

        if (! isset(self::$dictionaries[$lang])) {
            // $lang is not a language identifier
            $lang = self::$currentLanguage;
        }

        $value = self::$dictionaries[$lang];

        foreach ($keys as $key) {
            if (isset($value[$key])) {
              $value = $value[$key];
            } else if ($lang !== self::$defaultLanguage) {
              return self::get(self::$defaultLanguage.".".$originalKey, $replacements);
            }
        }

        if (! is_string($value)) {
            $value = $originalKey;
        }

        if (isset($replacements)) {
            foreach ($replacements as $key => $val) {
                $value = str_replace("{$key}", $val, $value);
            }
        }

        return $value;
    }
}
