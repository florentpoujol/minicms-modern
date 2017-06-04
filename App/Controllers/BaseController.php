<?php

namespace App\Controllers;

use App\App;

class BaseController
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

    public function getIndex($idOrSlug = null)
    {
        $this->render("main", "site index");
    }

    public function render($view, $pageTitle = null, $data = [])
    {
        if ($pageTitle === null) {
            $pageTitle = str_replace("/", ".", $view).".pagetitle";
        }
        $data["pageTitle"] = \App\Lang::get($pageTitle);

        extract($data);
        /*foreach ($data as $varName => $value) {
            ${$varName} = $value;
        }*/

        if (! isset($post)) {
            $post = [];
        }

        ob_start();
        require_once "../App/views/$view.php";
        $content = ob_get_clean();

        ob_start();
        require_once "../App/views/templates/".$this->template.".php";
        $content = ob_get_clean();

        foreach ($data as $varName => $value) {
            if (! is_array($value) && ! is_object($value)) {
                $content = str_replace('{'.$varName.'}', htmlspecialchars($value), $content);
            }
        }
        
        echo $content;
    }
}
