<?php

namespace App\Controllers;

use App\App;

class Controller
{
    /*
     * @var \App\Entities\User
     */
    protected $user;

    protected $template = "default";

    function __construct($user = null)
    {
        $this->user = $user;
    }

    public function getIndex()
    {
        $this->render("main", "site index");
    }

    public function render($view, $pageTitle = null, $data = [])
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


        if (! isset($pageTitle)) {
            $pageTitle = str_replace("/", ".", $view).".pagetitle";
        }
        
        $data["pageTitle"] = \App\Lang::get($pageTitle);
        foreach ($data as $key => $value) {
            if (! is_array($value) && ! is_object($value)) {
                $content = str_replace('{'.$key.'}', htmlspecialchars($value), $content);
            }
        }
        
        echo $content;
    }
}
