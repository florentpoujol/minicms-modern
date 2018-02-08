<?php

namespace App\Controllers;

use App\App;
use App\Config;
use App\Entities\Repositories\Menu as MenuRepo;
use App\Entities\User;
use App\Lang;
use App\Renderer;
use App\Session;
use App\Validator;
use App\Router;

class BaseController
{
    /**
     * The logged-in user
     * @var User
     */
    protected $user;

    /**
     * @var Lang
     */
    public $lang;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var Router
     */
    public $router;

    /**
     * @var Renderer
     */
    public $renderer;

    /**
     * @var Config
     */
    public $config;

    protected $template = "default";

    public function __construct(Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config)
    {
        $this->lang = $lang;
        $this->session = $session;
        $this->validator = $validator;
        $this->router = $router;
        $this->renderer = $renderer;
        $this->config = $config;
    }

    public function setLoggedInUser(User $user)
    {
        $this->user = $user; // logged in user
    }

    public function redirectIfUserLoggedIn(): bool
    {
        if ($this->user !== null) {
            $this->session->addError("user.alreadyloggedin");
            $this->router->redirect("admin");
            return true;
        }
        return false;
    }

    public function render(string $view, array $data = [])
    {
        $data["session"] = $this->session;
        $data["config"] = $this->config;
        $data["router"] = $this->router;
        $data["lang"] = $this->lang;
        $data["user"] = $this->user;

        $data["mainMenu"] = "install";
        if ($this->session->get("current_query_string") !== "install") {
            $data["mainMenu"] = App::$container->get(MenuRepo::class)->get(["in_use" => 1]);
        }

        if (!isset($data["pageTitle"])) {
            $data["pageTitle"] = str_replace("/", ".", $view) . ".pagetitle";
        }
        $data["pageTitle"] = $this->lang->get($data["pageTitle"]);

        if (!isset($data["post"])) {
            $data["post"] = []; // used to populate the forms
        }

        $this->renderer->render($this->template, $view, $data);
    }
}
