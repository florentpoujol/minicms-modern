<?php

namespace App;


class Renderer
{
    /**
     * @var Lang
     */
    protected $lang;

    /**
     * @var Config
     */
    public $config;

    protected $viewFolder = __dir__ . "/views";

    public function __construct(Lang $lang, Config $config)
    {
        $this->lang = $lang;
        $this->config = $config;
    }

    /**
     * The render is done from a static method so that the non-static content of the renderer instance do no bleed into the views.
     * Only the content of the data array is exposed
     */
    protected static function staticRender(string $path, array $data = [])
    {
        // exposes variables and data passed to the view
        /*if (!isset($data["post"])) {
            $data["post"] = [];
        }*/
        extract($data);

        require $path; // do not use require_once so that the same file can be included during tests
        // alternatively, the content can just be passed to eval
        // but saving to a file allow for easier debugging
    }

    public function render(string $template, string $view, array $data = null)
    {
        $content = $this->getViewContent($template, $view);

        $tempPath = $this->viewFolder . "/temp";
        file_put_contents($tempPath, $content);

        self::staticRender($tempPath, $data);
    }

    protected function getViewContent(string $template, string $view)
    {
        $viewContent = file_get_contents($this->viewFolder . "/$view.php");

        // process the includes instruction
        $viewContent = $this->processIncludes($viewContent);

        // includes the view's content inside the template
        $content = file_get_contents($this->viewFolder . "/templates/$template.php");
        $content = str_replace("{viewContent}", $viewContent, $content);

        // process template keywords
        $keywords = [
            "(foreach|for|if|elseif)( )?\((.+)\)" => "$1 ($3):",
            "(else)" => "$1:",
            "(endforeach|endfor|endif)" => "$1;"
        ];

        foreach ($keywords as $search => $replacement) {
            $content = preg_replace("/@$search/", "<?php $replacement ?>", $content);
        }

        // process template functions
        $functions = [
            "queryString" => ["router", "getQueryString"],
            "lang" => ["lang", "get"],
            "config" => ["config", "get"],
        ];

        foreach ($functions as $name => $funcData) {
            $matches = [];
            preg_match_all("/{" . "$name ([^}]+)}/", $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $content = str_replace(
                    $match[0],
                    '<?= $' . $funcData[0] . '->' . $funcData[1] . '("' . $match[1] . '"); ?>',
                    $content
                );
            }
        }

        // process template variables
        $content = preg_replace("/{([a-zA-Z0-9:$>\[\]\(\)_\"'-]+)}/", "<?= htmlentities($1); ?>", $content);

        return $content;
    }

    protected function processIncludes(string $viewContent): string
    {
        $matches = [];
        preg_match_all("/{include (.+)}/", $viewContent, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $includeContent = file_get_contents("$this->viewFolder/$match[1]");
            if (
                (substr_count($includeContent, "<?php") +
                substr_count($includeContent, "<?="))
                !== substr_count($includeContent, "?>")
            ) {
                // php tags MUST be closed in included files
                $includeContent .= "?>";
            }
            $viewContent = str_replace($match[0], $includeContent, $viewContent);
        }

        if (!empty($matches)) {
            // new content added, check if there is input inside
            $viewContent = $this->processIncludes($viewContent);
        }

        return $viewContent;
    }
}
