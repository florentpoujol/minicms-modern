<?php

namespace App\Controllers;

class BaseController
{
    /*
     * @var App\Entities\User
     */
    protected $user;

    protected $template = "default";

    /**
     * BaseController constructor.
     * @param \App\Entities\User $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /*public function getIndex($idOrSlug = null)
    {
        $this->render("main", "site index");
    }*/

    /**
     * @param string $view
     * @param string $pageTitle
     * @param array $data
     */
    public function render($view, $pageTitle = null, $data = [])
    {
        $viewContent = file_get_contents("../App/views/$view.php");

        // process the includes instruction
        $matches = [];
        preg_match_all("/{include (.+)}/", $viewContent, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $includeContent = file_get_contents($match[1]);
            if (substr_count($includeContent, "<?php") !== substr_count($includeContent, "?>")) {
                // php tags MUST be closed in included files
                $includeContent .= "?>";
            }
            $viewContent = str_replace($match[0], $includeContent, $viewContent);
        }

        // includes the view's content inside the template
        $content = file_get_contents("../App/views/templates/".$this->template.".php");
        $content = str_replace("{viewContent}", $viewContent, $content);

        // process template keywords
        $keywords = [
            "(foreach|for|if|elseif)( )?\((.+)\)" => "$1 ($3):",
            "(else)" => "$1:",
            "(endforeach|endfor|endif)" => "$1;"
        ];

        foreach ($keywords as $search => $replacement) {
            $content = preg_replace('/@'.$search.'/', '<?php '.$replacement.' ?>', $content);
        }

        // process template functions
        $functions = [
            "queryString" => ["\App\Route", "buildQueryString"],
            "lang" => ["\App\Lang", "get"],
        ];

        foreach ($functions as $name => $funcData) {
            $matches = [];
            preg_match_all("/{".$name." ([^}]+)}/", $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $content = str_replace(
                    $match[0],
                    '<?php echo '.$funcData[0].'::'.$funcData[1].'("'.$match[1].'"); ?>',
                    $content
                );
            }
        }

        // process template variables
        $content = preg_replace("/{([a-zA-Z0-9:$>\[\]\(\)_\"'-]+)}/", '<?php echo htmlentities($1); ?>', $content);

        // exposes variables and data passed to the view
        if ($pageTitle === null) {
            $pageTitle = str_replace("/", ".", $view).".pagetitle";
        }
        $pageTitle = \App\Lang::get($pageTitle);

        if (! isset($data["post"])) {
            $data["post"] = [];
        }
        extract($data);

        $tempPath = "../App/views/temp";
        file_put_contents($tempPath, $content);
        require_once $tempPath;
        // alternatively, the content can just be passed to eval
        // but saving to a file allow for easier debugging
    }
}
