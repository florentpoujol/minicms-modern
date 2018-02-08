<?php

namespace App\Entities;

use App\Config;
use App\Entities\Repositories\Menu as MenuRepo;
use App\Entities\Repositories\Page as PageRepo;
use App\Entities\Repositories\Post as PostRepo;
use App\Entities\Repositories\Category as CategoryRepo;
use App\Router;
use App\Session;

class Menu extends Entity
{
    public $title = "";
    public $json_structure = "[]";
    public $structure = []; // field not in DB, populated manually
    public $in_use = -1;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var MenuRepo
     */
    protected $menuRepo;

    /**
     * @var PageRepo
     */
    protected $pageRepo;

    /**
     * @var PostRepo
     */
    protected $postRepo;

    /**
     * @var CategoryRepo
     */
    protected $categoryRepo;

    public function __construct(
        Router $router, Config $config, Session $session,
        MenuRepo $menuRepo, PageRepo $pageRepo, PostRepo $postRepo, CategoryRepo $categoryRepo)
    {
        parent::__construct($router);
        $this->menuRepo = $menuRepo;
        $this->config = $config;
        $this->session = $session;
        $this->pageRepo = $pageRepo;
        $this->postRepo = $postRepo;
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * Remove items from structure where name and target are empty
     * @param array $structure The structure array, passed by reference
     */
    public static function cleanStructure(array $structure): array
    {
        for ($i = count($structure)-1; $i >= 0; $i--) {
            if (isset($structure[$i]["children"])) {
                $structure[$i]["children"] = self::cleanStructure($structure[$i]["children"]);
            }

            if (trim($structure[$i]["name"]) === "" && trim($structure[$i]["target"]) === "") {
                unset($structure[$i]);
            }
        }
        return $structure;
    }

    public function hydrate(array $data)
    {
        parent::hydrate($data);
        $this->structure = $this->getStructure();
    }

    public function update(array $data): bool
    {
        $data["json_structure"] = json_encode(
            self::cleanStructure($data["structure"]),
            JSON_PRETTY_PRINT
        ); // this is done here to that the new json_structure gets hydrated properly
        unset($data["structure"]);
        return parent::update($data);
    }

    public function getStructure(): array
    {
        return json_decode($this->json_structure, true);
    }

    public function isInUse(): bool
    {
        return $this->in_use === 1;
    }

    public function buildStructure(array $structure = null)
    {
        if ($structure === null){
            $structure = $this->getStructure();
        }
        $html = "<ul>";

        foreach ($structure as $i => $item) {
            $type = $item["type"] ?? "folder";
            $target = $item["target"] ?? "";
            $name = $item["name"] ?? "";
            $selected = "";

            if ($type !== "folder") { // page, post, category, homepage or external
                if ($type !== "external") { // page, post, category or homepage
                    if ($type === "homepage") {
                        $type = "page";
                    }
                    $repo = $type . "Repo"; // pageRepo, postRepo, categoryRepo

                    $field = "id";
                    if (!is_numeric($target)) {
                        $field = "slug";
                    }

                    $entity = $this->{$repo}->get([$field => $target]);

                    if ($entity !== false) {
                        if ($name === "") {
                            $name = $entity->title;
                        }

                        $field = "id";
                        if ($this->config->get("use_nice_rewrite")) {
                            $field = "slug";
                        }

                        $target = $this->router->getQueryString("$type/" . $entity->{$field});

                        $currentQueryString = $this->session->get("current_query_string"); // set in router

                        if ($currentQueryString === str_replace("?r=", "", $target)) {
                            $selected = "selected";
                        }
                    } else {
                        $name = "[content not found]";
                        $target = "";
                    }
                } // end if type !== external

                $html .= '<li class="' . $selected . '">
                <a href="' . $target . '">' . $name . '</a>';

            } else {
                $html .= "<li> $name";
            }

            if (isset($item["children"]) && count($item["children"]) > 0) {
                $html .= " &#9663;" . $this->buildStructure($item["children"]);
            }

            $html .= "</li>";
        }

        $html .= "</ul>";
        return $html;
    }
}
