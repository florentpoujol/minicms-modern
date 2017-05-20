<?php

function logout()
{
    Session::destroy();
    Messages::addSuccess("user.loggedout");
    redirect();
    exit;
}


function redirect($controller = "", $action = null, $params = [])
{
    if (isset($action) === true) {
        $params["a"] = $action;
    }

    $params["c"] = $controller;

    if ($controller !== "") {
        $strParams = "?";
        foreach ($params as $key => $value) {
            $strParams .= "$key=$value&";
        }
    }

    Messages::saveForLater();
    header("Location: index.php".rtrim($strParams, "&"));
    exit;
}

function isLoggedIn()
{
    global $user;
    return ($user !== false);
}
