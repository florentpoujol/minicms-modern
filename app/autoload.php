<?php

function __autoload($className)
{
    // $className = lcfirst($className).".php";
    // $folders = ["", "app", "controllers", "models"];
/*
    foreach ($folders as $folder) {
        $path = dirname(__FILE__)."/$folder/$className";
        var_dump($path);
        if (file_exists($path)) {
            var_dump($path);
            require_once($path);
        }
    }
    var_dump("----------");*/

    $className = str_replace("App\\", "", $className);
    $className = str_replace("\\", "/", $className);

    $path = __DIR__."/$className.php";
    var_dump($path);
    if (file_exists($path)) {
        var_dump($path);
        require_once($path);
    }
    var_dump("----------");
}
