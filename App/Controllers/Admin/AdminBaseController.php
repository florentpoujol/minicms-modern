<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Entities\User;
use App\Router;

class AdminBaseController extends BaseController
{
    protected $template = "defaultAdmin";

    public function setLoggedInUser(User $user)
    {
        // prevent commenters to access anything other than
        // - its user update page
        // - the list of its comments
        if (
            $user->isCommenter() &&
            (
                (strpos(strtolower($this->router->controllerName), "users") !== false &&
                    strpos(strtolower($this->router->methodName), "update") === false)
                ||
                (strpos(strtolower($this->router->controllerName), "comments") !== false &&
                    strpos(strtolower($this->router->methodName), "read") === false)
            )
        ) {
            $this->router->redirect("admin/users/update/$user->id");
        }

        $this->user = $user;
    }

    public function render(string $view, array $data = null)
    {
        parent::render("admin/$view", $data);
    }
}
