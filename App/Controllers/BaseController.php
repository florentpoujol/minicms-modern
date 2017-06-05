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
        $viewContent = file_get_contents("../App/views/$view.php");

        $matches = [];
        preg_match_all("/{include (.+)}/", $viewContent, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $includeContent = file_get_contents($match[1]);
            $viewContent = str_replace($match[0], $includeContent, $viewContent);
        }

        $content = file_get_contents("../App/views/templates/".$this->template.".php");
        $content = str_replace("{viewContent}", $viewContent, $content);

        // data
        if ($pageTitle === null) {
            $pageTitle = str_replace("/", ".", $view).".pagetitle";
        }
        $pageTitle = \App\Lang::get($pageTitle);

        $data["pageTitle"] = $pageTitle;
        if (! isset($data["post"])) {
            $data["post"] = [];
        }

        $data["user"] = $this->user;

        extract($data);

        // keywords
        $keywords = [
            // search => replacement
            "(foreach|for|if|elseif) \((.+)\)" => "$1 ($2):",
            "(else)" => "$1:",
            "(endforeach|endfor|endif)" => "$1;"
        ];

        $matches = [];
        foreach ($keywords as $search => $replacement) {
            $content = preg_replace('/@'.$search.'/', '<?php '.$replacement.' ?>', $content);
            /*foreach ($matches as $match) {
                $content = str_replace($match[0], '<?php echo htmlentities($'.$match[1].'); ?>', $content);
            }*/
        }


        // variables
        /*preg_match_all("/{([^} @]+)}/", $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $content = str_replace($match[0], '<?php echo htmlentities($'.$match[1].'); ?>', $content);
        }*/
        $content = preg_replace("/{([^} @]+)}/", '<?php echo htmlentities($$1); ?>', $content);

        // functions
        $functions = [
            "queryString" => ["\App\Route", "buildQueryString"],
            "lang" => ["\App\Lang", "get"],
        ];

        foreach ($functions as $name => $funcData) {
            $matches = [];
            preg_match_all("/{@".$name." ([^}]+)}/", $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $content = str_replace(
                    $match[0],
                    '<?php echo '.$funcData[0].'::'.$funcData[1].'("'.$match[1].'"); ?>',
                    $content
                );
            }
        }

        echo eval("?>".$content);
    }
}
