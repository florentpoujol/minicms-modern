<?php

namespace App;

class Config
{
    protected $configFile = __dir__ . "/../config/config.json";

    protected $config = [];

    public function __construct(string $configFile = null)
    {
        if ($configFile !== null) {
            $this->configFile = $configFile;
        }
        $realpath = realpath($this->configFile);
        if ($realpath !== false) { // happens during tests, leaving the old path also allows to debug
            $this->configFile = $realpath;
        }
    }

    public function getConfigFilePath(): string
    {
        return $this->configFile;
    }

    /**
     * read the config file (JSON) then populate the $config array
     */
    public function load(): bool
    {
        if (file_exists($this->configFile)) {
            $jsonConfig = file_get_contents($this->configFile);

            if (is_string($jsonConfig)) {
                $this->config = json_decode($jsonConfig, true);
                if ($this->config !== null) { // error while decoding
                    return true;
                }
                $this->config = [];
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
        return $this->config[$key] ?? $defaultValue;
    }

    /**
     * @param mixed $value
     */
    public function set(string $key, $value)
    {
        $this->config[$key] = $value;
    }
}
