<?php

namespace App\Controllers;

use App\Route;

class AdminController extends Controller
{
    function __construct()
    {
        parent::__construct();
        if (! isset($this->user)) {
            Route::redirect("admin/login");
        }

    }

    public function getIndex()
    {
        $this->render("admin/main", "admin index");
    }
}
