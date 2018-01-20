<?php

namespace App\Controllers;

use App\Entities\User;
use App\Lang;
use App\Renderer;
use App\Session;
use App\Validator;
use StdCmp\Router\Router;

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

    protected $template = "default";

    public function __construct(Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer)
    {
        $this->lang = $lang;
        $this->session = $session;
        $this->validator = $validator;
        $this->router = $router;
        $this->renderer = $renderer;
    }

    public function setLoggedInUser(User $user)
    {
        $this->user = $user; // logged in user
    }

    public function render(string $view, string $pageTitle = null, array $data = [])
    {
        $this->renderer->render($this->template, $view, $pageTitle, $data);
    }
}
