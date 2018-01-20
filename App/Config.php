<?php

namespace App;

class Config
{
    public $configFile = __dir__ . "/../config/config.json";

    public $config = [];

    /**
     * read the config file (JSON) then populate the $config array
     */
    public function load(string $configFile = null): bool
    {
        if ($configFile === null) {
            $configFile = $this->configFile;
        }
        if (file_exists($configFile)) {
            $jsonConfig = file_get_contents($configFile);

            if (is_string($jsonConfig)) {
                $this->config = json_decode($jsonConfig, true);
                return true;
            }
        }
        return false;
    }

    public function fileExists(): bool
    {
        return file_exists($this->configFile);
    }

    /**
     * Write the content of the $config array as JSON in a file
     * @return bool True on success, false otherwise.
     */
    public function save(): bool
    {
        $jsonConfig = json_encode($this->config, JSON_PRETTY_PRINT);
        return (bool)file_put_contents($this->configFile, $jsonConfig);
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
