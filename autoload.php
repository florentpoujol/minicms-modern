<?php

function minimvc_mvc_autoload($className)
{
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
    $path = __DIR__ . "/" . $className . ".php";
    if (file_exists($path)) {
        require_once($path);
    }
}

spl_autoload_register("minimvc_mvc_autoload");
