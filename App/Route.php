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
    /**
     * List of the expected routes regexes
     * First item is default route.
     */
    private static $routes = [
        "blog",
        "(page|post|category)/([a-z0-9]+)",
        "logout",
        "login/?(lostpassword|resetpassword)?",
        "register/?(resendconfirmationemail|confirmemail)?",
        "admin/?(users|pages|posts|comments|categories|config|medias|menus)?/?([0-9]+)?/?(create|update|delete)?",
    ];

    /**
     * Check the current request URI against the possible routes.
     * Then extract the data from the route (controller, method, resource id or slug, action)
     * @param Entities\User $user The user, to pass to the controller. Null when user is guest
     * @return void
     **/
    public static function load($user = null)
    {
        $r = isset($_GET["r"]) ? $_GET["r"] : "";
        // $token = isset($_GET["token"]) ? $_GET["token"] : "";
        // $p = isset($_GET["p"]) ? (int)$_GET["p"] : 1;

        $routeOk = false;
        foreach (self::$routes as $route) {
            $route = str_replace("/", "\\/", $route);
            if (preg_match('/^'.$route.'$/', $r) === 1) {
                $routeOk = true;
                break;
            }
        }

        if (! $routeOk) {
            // when the request URI do not match any of the routes
            $r = self::$routes[0]; // TODO: send to 404 page instead of default route
        }


        $parts = explode("/", $r);
        $controller = $parts[0];

        if ($controller === "logout") {
            self::logout();
        }

        // controller method when in login, register controllers
        // controller name when in admin section
        // or slug or id when viewing a page, post or category
        $page = "index";
        if (isset($parts[1])) {
            if (is_numeric($parts[1])) {
                // todo: slugs must not begin by a number
                $parts[1] = (int)$parts[1];
            }
            $page = $parts[1];
        }

        // is passed to the controller's method
        $idOrSlug = null;
        $resourceControllers = ["page", "post", "category"];

        if (isset($parts[2])) {
            // that's only when controller is admin
            $idOrSlug = (int)$parts[1]; // always id
        } else if (in_array($controller, $resourceControllers)) {
            $idOrSlug = $page;
            $page = "Index";
        }

        // CRUD. method name for the admin controllers
        $action = isset($parts[3]) ? $parts[3] : "Read";

        if ($controller === "admin") {
            if ($page === "index") {
                // no specific parts of the admin section is asked for
                // - call AdminBaseController::getRead() and let it do whatever it wants (show admin dashboard maybe)
                // - or call a default admin section page (done here)
                $page = "Users";
            }

            $controller = "Admin\\".ucfirst($page);
            $page = $action;
        }

        $controllerName = "\App\Controllers\\".ucfirst($controller);
        $controller = new $controllerName($user);

        $methodName = App::$requestMethod.$page;
        $controller->{$methodName}($idOrSlug);
    }

    public static function buildQueryString($route = "", $params = [])
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
        $uri = self::buildQueryString($route, $params);
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
