<?php

function __autoload($className)
{
    $className = str_replace("App\\", "", $className);
    $className = str_replace("\\", "/", $className);
    $path = __DIR__."/".$className.".php";

    if (file_exists($path)) {
        require_once($path);
    }
}
