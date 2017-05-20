<?php

class Controller
{
    protected $user = false;

    function __construct()
    {
        global $user;
        $this->user = $user;
    }

    public function getIndex()
    {
        loadView("main", "site index");
    }
}
