<?php

namespace App\Controllers;

class Controller
{
    protected $user = false;

    protected $template = "default"; // usually changed in controllers contructor

    function __construct()
    {
        global $user;
        $this->user = $user;
    }

    public function getIndex()
    {
        $this->render("main", "site index");
    }

    public function render($view, $pageTitle, $data = [])
    {      
        foreach ($data as $varName => $value) {
            ${$varName} = $value;
        }

        ob_start();
        require_once "../app/views/$view.php";
        $content = ob_get_clean();

        ob_start();
        require_once "../app/views/templates/".$this->template.".php";
        $content = ob_get_clean();

        $data["pageTitle"] = \App\Lang::get($pageTitle);
        foreach ($data as $key => $value) {
            $content = str_replace('{'.$key.'}', htmlspecialchars($value), $content);    
        }
        
        echo $content;
    }
}
