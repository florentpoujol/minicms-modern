<?php

namespace App\Controllers;

use App\Route;

class AdminController extends Controller
{
    function __construct($user)
    {
        parent::__construct($user);
        if (! isset($this->user)) {
            Route::redirect("login");
        }

    }

    public function getIndex()
    {
        $this->render("admin/main", "admin index");
    }
}
