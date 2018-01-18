<?php

namespace App;
use App\Entities\User;

/**
 * Class Route
 * Process the query strings.
 * There always be at least one : r for the route itself, see the $routes array.
 * Other query strings may be token, or page
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
    public static function load(User $user)
    {
        $r = $_GET["r"] ?? "blog";

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
            // todo: properly redirect to a 404 page
            echo "404";
            exit;
        }

        $callback = function ($arg) { return is_numeric($arg) ? (int)$arg : $arg; };
        $controllerArgs = array_map($callback, $controllerArgs);

        $controllerName = $routeName;
        $methodName = $routeName;

        switch ($routeName) {
            case "logout":
                self::logout();
                break;

            case "lostpassword":
            case "resetpassword":
                $controllerName = "Login";
                break;

            case "resendconfirmationemail":
            case "confirmemail":
                $controllerName = "Register";
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

        $controllerName = implode("\\", $parts);
        $controllerName = "\App\Controllers\\$controllerName";
        self::$controllerName = $controllerName;

        $methodName = App::$requestMethod . $methodName;
        self::$methodName = $methodName;

        $controller = new $controllerName($user);
        $controller->{$methodName}(...$controllerArgs);
    }

    public static function buildQueryString(string $route = ""): string
    {
        if (Config::get("use_nice_url") !== true && $route !== "") {
            $route = "?r=$route";
        }

        return $route;
    }

    public static function getURL(string $route = ""): string
    {
        return App::$url . self::buildQueryString($route);
    }

    public static function redirect(string $route = "")
    {
        Messages::save();
        header("Location: " . self::getURL($route));
        exit;
    }

    public static function toInstall()
    {
        $controller = new \App\Controllers\Install(null);
        $methodName = App::$requestMethod . "Install";
        $controller->{$methodName}();
    }

    public static function logout()
    {
        Session::destroy();
        self::redirect();
    }
}
