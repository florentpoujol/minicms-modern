<?php

namespace App;

class Route
{
    /**
     * Check the current request URI against the provided possible routes
     * Called from the index script
     * @param $routes array An array of regexes (string) representing potential routes
     * @return void
     **/
    public static function load($routes)
    {
        $r = isset($_GET["r"]) ? $_GET["r"] : "";
        // $token = isset($_GET["token"]) ? $_GET["token"] : "";
        // $p = isset($_GET["p"]) ? (int)$_GET["p"] : 1;

        $routeOk = false;
        foreach ($routes as $route) {
            $route = str_replace("/", "\\/", $route);
            if (preg_match('/^'.$route.'$/', $r) === 1) {
                $routeOk = true;
                break;
            }
        }

        if (! $routeOk) {
            $r = $routes[0]; // TODO: send to 404 instead of sending to default route
        }

        $parts = explode("/", $r);
        $controller = $parts[0];

        if ($controller === "logout") {
            self::logout();
        }

        $page = "index"; // page name (when in admin, login, register) or slug or id (when viewing page or posts)
        if (isset($parts[1])) {
            if (is_numeric($parts[1])) {
                $parts[1] = (int)$parts[1];
            }
            $page = $parts[1];
        }

        $id = null;
        if (isset($parts[2])) {
            // only exists when in admin section
            $id = (int)$parts[1];
        }

        $action = isset($parts[3]) ? $parts[3] : null;

        if ($controller === "admin") {
            if ($page !== "index") {
                $controller = $page;
                $page = $action;
            }
        }

        $controllerName = "\App\Controllers\\".$controller."Controller";
        global $user;
        $controller = new $controllerName($user);

        $methodName = App::$requestMethod.$page;
        $controller->{$methodName}($id);
    }

    public static function uri($route = "", $params = [])
    {
        $uri = "";
        if ($route !== "") {
            $uri = "?r=$route";
        }

        foreach ($params as $key => $value) {
            $uri .= "&$key=$value";
        }

        return $uri;
    }

    public static function redirect($route = null, $params = null)
    {
        Messages::save();
        $uri = self::uri($route, $params);
        header("Location: ".App::$url.$uri);
        exit;
    }

    public static function logout()
    {
        Session::destroy();
        // TODO destroy cookie
        self::redirect();
    }
}
