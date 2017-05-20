<?php

class AdminController extends Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->user === false) {
            redirect("login");
        }

        loadView("admin/main", "admin index");
    }


}
