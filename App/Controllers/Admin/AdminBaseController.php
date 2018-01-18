<?php

namespace App\Controllers\Admin;

use App\Entities\User;
use App\Lang;
use App\Route;
use App\Session;
use App\Validator;

class AdminBaseController extends \App\Controllers\BaseController
{
    function __construct(User $user, Lang $localization, Session $session, Validator $validator)
    {
        parent::__construct($user, $localization, $session, $validator);

        if (! isset($this->user)) {
            Route::redirect("login");
        }

        $this->template = "defaultAdmin";

        // prevent commenters to access anything other than
        // - its user update page
        // - the list of its comments
        if (
            $this->user->isCommenter() &&
            (
                (strpos(strtolower(Route::$controllerName), "users") !== false &&
                strpos(strtolower(Route::$methodName), "update") === false)
                ||
                (strpos(strtolower(Route::$controllerName), "comments") !== false &&
                strpos(strtolower(Route::$methodName), "read") === false)
            )
        )
        {
            Route::redirect("admin/users/update/".$this->user->id);
        }
    }

    public function render(string $view, string $pageTitle = null, array $data = [])
    {
        parent::render("admin/$view", $pageTitle, $data);
    }
}
