<?php

function minimvc_mvc_autoload($className)
{
    $path = __DIR__ . "/" . $className . ".php";
    if (file_exists($path)) {
        require_once($path);
    }
}

spl_autoload_register("minimvc_mvc_autoload");
