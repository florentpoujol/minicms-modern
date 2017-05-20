<?php

namespace App;

class App
{
    public static $protocol = "http";

    public static $host = "localhost";

    // path, if any from the host to the index's directory
    // ends with a trailing slash
    public static $directory = "/";

    // current full site URL
    public static $url = "";

    public static $requestMethod = "get";

    public static function load()
    {
        self::$protocol = $_SERVER["REQUEST_SCHEME"];
        self::$host = $_SERVER["HTTP_HOST"];
        self::$directory = str_replace("index.php", "", $_SERVER["SCRIPT_NAME"]);
        self::$url = self::$protocol."://".self::$host.self::$directory;

        self::$requestMethod = strtolower($_SERVER["REQUEST_METHOD"]);
    }
}
