<?php

namespace App;

use StdCmp\DI\DIContainer;

class App
{
    /**
     * @var DIContainer
     */
    public static $container;

    public function __construct(DIContainer $container)
    {
        self::$container = $container;

        $config = self::$container->get(Config::class);
        $config->set("upload_path", realpath(__dir__ . "/../public/uploads"));

        $directory = str_replace("index.php", "", $_SERVER["SCRIPT_NAME"]); // trailing slash
        $config->set("site_directory", $directory);

        $scheme = $_SERVER["REQUEST_SCHEME"] ?? "http";
        $url = "$scheme://$_SERVER[HTTP_HOST]$directory";
        $config->set("site_url", $url);
    }
}
