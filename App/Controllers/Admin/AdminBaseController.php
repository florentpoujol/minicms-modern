<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Entities\User;

class AdminBaseController extends BaseController
{
    protected $template = "defaultAdmin";

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

    public function render(string $view, array $data = null)
    {
        parent::render("admin/$view", $data);
    }
}
