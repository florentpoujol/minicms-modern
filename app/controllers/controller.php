<?php

namespace App\Controllers;

class Controller
{
    protected $user = false;

    protected $template = "default";

    function __construct()
    {
        global $user;
        $this->user = $user;
    }

    public function getIndex()
    {
        $this->render("main", "site index");
    }

    public function render($view, $data = [])
    {
        ob_start();
        require_once "../app/views/$view.php";
        $content = ob_get_clean();

        require_once "../app/views/templates/".$this->template.".php";
    }
}
