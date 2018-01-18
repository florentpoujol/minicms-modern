<?php

namespace App;

use Psr\Container\ContainerInterface;
use StdCmp\DI\DIContainer;

class App
{
    public $protocol = "http";

    public $host = "localhost";

    /**
     * Path, if any from the host to the index's directory.
     * Ends with a trailing slash.
     */
    public $directory = "/";

    /**
     * Current full site URL without query string.
     * Ends with a trailing slash.
     */
    public $url = "";

    public $requestMethod = "get";

    public $uploadPath = "";

    /**
     * @var ContainerInterface
     */
    public $container;

    public function __construct($container)
    {
        $this->container = $container;

        $this->setupRequestInfo();
    }

    public function setupRequestInfo()
    {
        $this->protocol = $_SERVER["REQUEST_SCHEME"];
        $this->host = $_SERVER["HTTP_HOST"];
        $this->directory = str_replace("index.php", "", $_SERVER["SCRIPT_NAME"]); // trailing slash
        $this->url = $this->protocol . "://" . $this->host . $this->directory;

        $this->requestMethod = strtolower($_SERVER["REQUEST_METHOD"]);

        // $this->uploadPath = trim(Config::get("upload_folder"), "/") . "/";
        $configManager = $this->container->get(Config::class);
        $this->uploadPath = trim($configManager->get("upload_folder"), "/") . "/";
    }
}
