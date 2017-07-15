<?php

namespace App;

/**
 * Class Route
 * Process the query strings.
 * There always be at least one : r for the route itself, see the $routes array.
 * Other query strings may be token, or page
 * @package App
 */
class Route
{
    public static $controllerName = "";
    public static $methodName = "";

    /**
     * List of the expected routes regexes
     * First item is default route.
     */
    private static $routes = [
        "blog" => "blog/?([0-9]+)?",
        "page" => "page/([a-z0-9]+)",
        "post" => "post/([a-z0-9]+)",
        "category" => "category/([a-z0-9]+)/?([0-9]+)?",
        "logout" => "logout",

        "login" => "login",
        "lostpassword" => "login/lostpassword",
        "resetpassword" => "login/resetpassword/([0-9]+)/([a-zA-Z0-9]+)",

        "register" => "register",
        "resendconfirmationemail" => "register/resendconfirmationemail",
        "confirmemail" => "register/confirmemail/([0-9]+)/([a-zA-Z0-9]+)",

        "admin_config" => "admin/config",
        "admin" => "admin/?(users|pages|posts|comments|categories|medias|menus)?/?(create|read|update|delete)?/?([0-9]+)?",
        // a URI like this one might be confusing :  admin/users/read/2
        // it show the page 2 of the users list, it does not display the user with id 2
        // there is no "profile" page where data is just displayed, non admin immediately access their edit page
    ];


    /**
     * Check the current request URI against the possible routes.
     * Then extract the data from the route (controller, method, resource id or slug, action)
     * @param Entities\User $user The user, to pass to the controller. Null when user is guest
     * @return void
     */
    public static function load($user = null)
    {
        $r = isset($_GET["r"]) ? $_GET["r"] : "blog";

        $routeName = "";
        $controllerArgs = [];
        foreach (self::$routes as $name => $route) {
            $route = str_replace("/", "\\/", $route);

            if (preg_match('/^'.$route.'$/', $r, $controllerArgs) === 1) {
                array_shift($controllerArgs); // index 0 is the whole match
                $routeName = $name;
                break;
            }
        }

        if ($routeName === "") {
            // todo: properly redirect to 404
            echo "404";
            exit;
        }

        $cb = function ($arg) { return is_numeric($arg) ? (int)$arg : $arg; };
        $controllerArgs = array_map($cb, $controllerArgs);

        $controllerName = $routeName;
        $methodName = "Index";

        switch ($routeName) {
            case "logout":
                self::logout();
                break;

            case "login":
            case "register":
                $methodName = $routeName;
                break;

            case "lostpassword":
            case "resetpassword":
                $controllerName = "Login";
                $methodName = $routeName;
                break;

            case "resendconfirmationemail":
            case "confirmemail":
                $controllerName = "Register";
                $methodName = $routeName;
                break;

            case "admin_config":
                $controllerName = "Admin\Config";
                $methodName = "Update";
                break;

            case "admin":
                $controllerName = "Admin\\Users";
                if (isset($controllerArgs[0])) {
                    $controllerName = "Admin\\".$controllerArgs[0];
                    array_shift($controllerArgs);
                }

                $methodName = "Read";
                if (isset($controllerArgs[0])) {
                    $methodName = $controllerArgs[0];
                    array_shift($controllerArgs);
                }
                break;
        }

        $parts = explode("\\", $controllerName);
        $parts = array_map(function($val){ return ucfirst($val); }, $parts);
        $controllerName = join("\\", $parts);
        $controllerName = "\App\Controllers\\$controllerName";
        $methodName = App::$requestMethod.$methodName;

        self::$controllerName = $controllerName;
        self::$methodName = $methodName;

        $controller = new $controllerName($user);
        $controller->{$methodName}(...$controllerArgs);
    }

    public static function buildQueryString($route = "", ...$additionnalArgs)
    {
        /*if (isset(self::$routes[$route])) {
            $route = self::$routes[$route];
        }

        $parts = explode("/", $route);
        $id = 0;
        foreach ($parts as $part) {
            if (preg_match("/^(.+)$/", $part) === 1) {
                $id++;
                $replace = "";
                if (isset($additionnalArgs[$id])) {
                    $replace = $additionnalArgs[$id];
                }
                $route = str_replace($part, $replace, $route);
            }
        }

        $route = trim($route, "/");*/

        if (Config::get("use_nice_url") !== true && $route !== "") {
            $route = "?r=$route";
        }

        return $route;
    }

    public static function getURL($route = "")
    {
        return App::$url.self::buildQueryString($route);
    }

    public static function redirect($route = "")
    {
        Messages::save();
        header("Location: ".self::getURL($route));
        exit;
    }

    public static function logout()
    {
        Session::destroy();
        self::redirect();
    }
}
