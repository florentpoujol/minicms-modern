<?php

namespace App\Controllers\Admin;

use App\Route;

class AdminBaseController extends \App\Controllers\BaseController
{
    /**
     * AdminBaseController constructor.
     * @param \App\Entities\User $user
     */
    function __construct($user)
    {
        parent::__construct($user);

        if (! isset($this->user)) {
            Route::redirect("login");
        }

        $this->template = "defaultAdmin";

        if (
            $this->user->isCommenter() &&
            (
                (Route::$controllerName === "users" &&
                strpos(Route::$methodName, "Update") === false)
                ||
                (Route::$controllerName === "comments" &&
                strpos(Route::$methodName, "Read") === false)
            )
        )
        {
            // prevent commenters to access anything other than
            // - its user update page
            // - the list of its comments
            Route::redirect("admin/users/update/".$this->user->id);

        }
    }

    public function render($view, $pageTitle = null, $data = [])
    {
        parent::render("admin/$view", $pageTitle, $data);
    }

    public function getRead()
    {
        $this->render("admin/main", "admin index");
    }
}
