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

    public static $languageFolder = __DIR__."/../languages/";

    /**
     * dictionaries per language
     */
    public static $dictionaries = [];

    /**
     * @param string $lang The language identifier ie: en, fr, de, ...
     * @return bool Returns true when the language has been loaded during this function call, false otherwise.
     */
    public static function load(string $lang, bool $reload = false): bool
    {
        if ($reload || !isset(self::$dictionaries[$lang])) {
            $path = self::$languageFolder . "$lang.php";
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
     */
    public static function get(string $keys, array $replacements = null): string
    {
        $originalKey = $keys;
        $keys = explode(".", $keys);
        $lang = $keys[0]; // either the language or the actual first key part

        if (! isset(self::$dictionaries[$lang])) {
            self::load($lang);
        }

        if (! isset(self::$dictionaries[$lang])) {
            // $keys[0] (and $lang) are not a language identifier
            $lang = self::$currentLanguage;
        } else {
            array_shift($keys); // remove the language identifier from the keys
        }

        $value = self::$dictionaries[$lang];

        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                break;
            }
            $value = $value[$key];
        }

        if (is_array($value)) { // key do not lead to a string
            if ($lang !== self::$defaultLanguage) {
                $defaultLangKey = self::$defaultLanguage.".".preg_replace("/^$lang\./", "", $originalKey);
                $value = self::get($defaultLangKey, $replacements);
                if ($value !== $defaultLangKey) { // found in the default language
                    return $value;
                }
            }

            $value = $originalKey;
        } elseif (isset($replacements)) { // value is a string here
            foreach ($replacements as $replKey => $newValue) {
                $value = str_replace('{' . $replKey . '}', $newValue, $value);
            }
        }

        return $value;
    }
}
