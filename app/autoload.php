<?php

function __autoload($className)
{
    $className = str_replace("App\\", "", $className);

    $otherSeparator = "\\";
    if (DIRECTORY_SEPARATOR === "\\") {
        $otherSeparator = "/";
    }

    $className = str_replace($otherSeparator, DIRECTORY_SEPARATOR, $className);

    $path = __DIR__.DIRECTORY_SEPARATOR.strtolower($className).".php";
    // var_dump($path);
    if (file_exists($path)) {
        // var_dump("loaded");
        require_once($path);
    }
}
