<?php

namespace App;

class Config
{
    public static $configFolder = __dir__ . "/../config/";

    public static $config = [];

    /**
     * read the config file (JSON) then populate the $config array
     */
    public static function load(): bool
    {
        $path = self::$configFolder . "config.json";
        if (file_exists($path)) {
            $jsonConfig = file_get_contents($path);

            if (is_string($jsonConfig)) {
                self::$config = json_decode($jsonConfig, true);
                return true;
            }
        }
        return false;
    }

    /**
     * Write the content of the $config array as JSON in a file
     * @return bool True on success, false otherwise.
     */
    public static function save(): bool
    {
        $jsonConfig = json_encode(self::$config, JSON_PRETTY_PRINT);
        return (bool)file_put_contents(self::$configFolder . "config.json", $jsonConfig);
    }

    /**
     * @param mixed|null $defaultValue
     * @return mixed|null
     */
    public static function get(string $key, $defaultValue = null)
    {
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }

        return $defaultValue;
    }

    /**
     * @param mixed $value
     */
    public static function set(string $key, $value)
    {
        self::$config[$key] = $value;
    }
}
