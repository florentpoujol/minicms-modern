<?php

namespace App;

class Route
{
    public static function load($routes)
    {
        $r = isset($_GET["r"]) ? $_GET["r"] : "";
        $token = isset($_GET["token"]) ? $_GET["token"] : "";
        $p = isset($_GET["p"]) ? (int)$_GET["p"] : 1;

        $routeOk = false;
        foreach ($routes as $route) {
            $route = str_replace("/", "\\/", $route);
            if (preg_match('/^'.$route.'$/', $r) === 1) {
                // keep loose comparison, the function returns 0 when not found or false on error
                var_dump($route);
                $routeOk = true;
                break;
            }
        }

        if (! $routeOk) {
            $r = $routes[0];
        }

        $parts = explode("/", $r);
        $controller = $parts[0];

        $page = "Index"; // page name (when in admin, login, register) or slug or id
        if (isset($parts[1])) {
            if (is_numeric($parts[1])) {
                $parts[1] = (int)$parts[1];
            }
            $page = $parts[1];
        }

        $id = null;
        if (isset($parts[2])) {
            $id = (int)$parts[1];
        }

        $action = isset($parts[3]) ? $parts[3] : null;

        if ($controller === "admin") {
            $controller = $page;
            $page = $action;
        }

        $controllerName = "\App\Controllers\\".$controller."Controller";
        global $user;
        $controller = new $controllerName($user);

        $methodName = App::$requestMethod.$page;
        $controller->{$methodName}($id);
    }

    public static function redirect($route = "")
    {
        /*
         * if (isset($action) === true) {
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
         */
    }

    public static function logout()
    {
        Session::destroy();
        // TODO destroy cookie
        self::redirect();
    }

    public static function link($route)
    {

    }
}
