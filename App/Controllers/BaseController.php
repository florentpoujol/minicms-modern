<?php

namespace App\Controllers;

use App\Config;
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
    protected $lang;

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

    public function render(string $view, array $data = [])
    {
        $data["session"] = $this->session;
        $data["config"] = $this->config;
        $data["router"] = $this->router;

        if (!isset($data["pageTitle"])) {
            $data["pageTitle"] = str_replace("/", ".", $view) . ".pagetitle";
        }
        $data["pageTitle"] = $this->lang->get($data["pageTitle"]);

        $this->renderer->render($this->template, $view, $data);
    }
}
