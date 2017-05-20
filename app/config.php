<?php

namespace App;

class Config
{
    // pth to the configuration file
    // set from the index file
    public static $configPath = "";

    private static $config = [];

    // read the config file (JSON) then populate the $config array
    public static function load()
    {
        $jsonConfig = file_get_contents(self::$configPath);

        if (is_string($jsonConfig)) {
            $config = json_decode($jsonConfig, true);
            return true;
        }

        return false;
    }

    // write the content of the $config array as JSON in a file
    /// returns true on success, false otherwise
    public static function save()
    {
        $jsonConfig = json_encode(self::$config);
        return (file_put_contents(self::$configPath, $jsonConfig));
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
