<?php

namespace App;

use App\Controllers\Install;
use App\Entities\User;
use StdCmp\DI\DIContainer;

/**
 * Class Router
 * Process the query strings.
 * There always be at least one : r for the route itself, see the $routes array.
 * Other query strings may be token, or page
 */
class Router
{
    public $controllerName = "";
    public $methodName = "";

    /**
     * @var DIContainer
     */
    public $container;

    /**
     * @var Config
     */
    public $config;

    /**
     * @var Session
     */
    public $session;

    /**
     * List of the expected routes regexes
     * First item is default route.
     */
    private $routes = [
        "blog" => "blog/?([0-9]+)?",
        "page" => "page/([a-z0-9-]+)",
        "post" => "post/([a-z0-9-]+)",
        "category" => "category/([a-z0-9-]+)/?([0-9]+)?",

        "logout" => "logout",
        "login" => "login",
        "lostpassword" => "login/lostpassword",
        "resetpassword" => "login/resetpassword/([0-9]+)/([a-zA-Z0-9]+)",

        "register" => "register",
        "resendconfirmationemail" => "register/resendconfirmationemail",
        "confirmemail" => "register/confirmemail/([0-9]+)/([a-zA-Z0-9]+)",

        "admin_config" => "admin/config",
        "admin" => "admin/?(?:users|pages|posts|comments|categories|medias|menus)?/?(?:create|read|update|delete)?/?([0-9]+)?",
        // a URI like this one might be confusing :  admin/users/read/2
        // it show the page 2 of the users list, it does not display the user with id 2
        // there is no "profile" page where data is just displayed, non admin immediately access their edit page
    ];

    public function __construct(DIContainer $container, Config $config, Session $session)
    {
        $this->container = $container;
        $this->config = $config;
        $this->session = $session;
    }

    /**
     * Check the current request URI against the possible routes.
     * Then extract the data from the route (controller, method, resource id or slug, action)
     * @param Entities\User $user The user, to pass to the controller. Null when user is guest
     * @return void
     */
    public function load(User $user = null)
    {
        $r = $_GET["r"] ?? "blog";
        if (trim($r) === "") {
            $r = "blog";
        }

        $routeName = "";
        $controllerArgs = [];
        $routeParts = [];
        foreach ($this->routes as $name => $route) {
            if (preg_match("~^" . $route . '$~', $r, $controllerArgs) === 1) {
                array_shift($controllerArgs); // index 0 is the whole match
                $routeName = $name;
                $routeParts = explode("/", $r);
                break;
            }
        }

        if ($routeName === "") {
            // todo: properly redirect to a 404 page
            echo "404";
            exit;
        }

        $this->session->set("current_query_string", $r);

        $controllerName = ucfirst($routeParts[0]);
        $methodName = $controllerName;

        switch ($routeName) {
            case "logout":
                $this->logout();
                return;

            case "blog":
            case "page":
            case "post":
            case "category":
            case "register":
            case "login":
                $methodName = $controllerName; //  ie: getPost(int $postId)
                break;

            case "lostpassword":
            case "resetpassword":
            case "resendconfirmationemail":
            case "confirmemail":
                $methodName = ucfirst($routeParts[1]); // ie: getLostpassword
                break;

            case "admin_config":
                $controllerName = "Admin\Config";
                $methodName = "Update";
                break;

            case "admin":
                $controllerName = "Admin\\" . ucfirst($routeParts[1] ?? "Users");
                $methodName = ucfirst($routeParts[2] ?? "Read");
                break;

            default:
                throw new \UnexpectedValueException("Unhandled route '$routeName'.");
                break;
        }

        $this->controllerName = "\App\Controllers\\$controllerName";
        $controller = $this->container->make($this->controllerName);
        if ($user !== null) {
            $controller->setLoggedInUser($user);
        }

        if (
            strpos($this->controllerName, "Admin") !== false &&
            $controller->redirectIfUserIsGuest()
        ) {
            return;
        }

        $this->methodName = strtolower($_SERVER["REQUEST_METHOD"] ?? "get") . $methodName;
        $controller->{$this->methodName}(...$controllerArgs);
    }

    public function getQueryString(string $route = ""): string
    {
        if ($this->config->get("use_nice_url") !== true && $route !== "") {
            $route = "?r=$route";
        }
        return $route;
    }

    public function getURL(string $route = ""): string
    {
        return $this->config->get("site_url") . $this->getQueryString($route);
    }

    public function redirect(string $route = "")
    {
        header("Location: " . $this->getURL($route));
    }

    public function toInstall()
    {
        $this->session->set("current_query_string", "install");

        $controller = $this->container->make(Install::class);
        $methodName = strtolower($_SERVER["REQUEST_METHOD"] ?? "get") . "Install";
        $controller->{$methodName}();
    }

    public function logout()
    {
        $this->session->destroy();
        $this->redirect();
    }
}
