<?php

namespace App\Controllers\Admin;

use App\Config;
use App\Controllers\BaseController;
use App\Entities\User;
use App\Form;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;

class AdminBaseController extends BaseController
{
    /**
     * @var Form
     */
    protected $form;

    protected $template = "defaultAdmin";

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config, Form $form)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config);
        $this->form = $form;
    }

    public function setLoggedInUser(User $user)
    {
        // prevent commenters to access anything other than the users or comments controllers
        if (
            $user->isCommenter() &&
            strpos(static::class, "Users") === false &&
            strpos(static::class, "Comments") === false
        ) {
            $this->router->redirect("admin/users/update/$user->id");
        }

        $this->user = $user;
    }

    public function redirectIfUserIsGuest(): bool
    {
        if ($this->user === null) {
            $this->router->redirect("login");
            return true;
        }
        return false;
    }

    public function render(string $view, array $data = null)
    {
        $data["form"] = $this->form;
        if (!isset($data["pageTitle"])) {
            $data["pageTitle"] = "admin.$view";
        }
        parent::render("admin/$view", $data);
    }
}
