<?php

namespace App;

class Config
{
    public static $configFolder = __DIR__."/../config/";

    private static $config = [];

    /**
     * read the config file (JSON) then populate the $config array
     */
    public static function load()
    {
        $jsonConfig = file_get_contents(self::$configFolder."config.json");

        if (is_string($jsonConfig)) {
            self::$config = json_decode($jsonConfig, true);
            return true;
        }

        return false;
    }

    /**
     * write the content of the $config array as JSON in a file
     * @return true on success, false otherwise
     */
    public static function save()
    {
        $jsonConfig = json_encode(self::$config, JSON_PRETTY_PRINT);
        return (bool)file_put_contents(self::$configFolder."config.json", $jsonConfig);
    }

    public static function get($key, $defaultValue = null)
    {
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }

        return $defaultValue;
    }

    public static function set($key, $value)
    {
        self::$config[$key] = $value;
    }
}
