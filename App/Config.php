<?php

namespace App;

class Config
{
    public $configFolder = __dir__ . "/../config/";

    public $config = [];

    public function __construct(string $configFolder = null)
    {
        if ($configFolder !== null) {
            // during tests
            $this->configFolder = $configFolder;
        }

        $this->load();
    }

    /**
     * read the config file (JSON) then populate the $config array
     */
    public function load(): bool
    {
        $path = $this->configFolder . "config.json";
        if (file_exists($path)) {
            $jsonConfig = file_get_contents($path);

            if (is_string($jsonConfig)) {
                $this->config = json_decode($jsonConfig, true);
                return true;
            }
        }
        return false;
    }

    public function fileExists(): bool
    {
        return file_exists($this->configFolder . "config.json");
    }

    /**
     * Write the content of the $config array as JSON in a file
     * @return bool True on success, false otherwise.
     */
    public function save(): bool
    {
        $jsonConfig = json_encode($this->config, JSON_PRETTY_PRINT);
        return (bool)file_put_contents($this->configFolder . "config.json", $jsonConfig);
    }

    /**
     * @param mixed|null $defaultValue
     * @return mixed|null
     */
    public function get(string $key, $defaultValue = null)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        return $defaultValue;
    }

    /**
     * @param mixed $value
     */
    public function set(string $key, $value)
    {
        $this->config[$key] = $value;
    }
}
