<?php

namespace App\Controllers;

use \App\Entities\Page as PageEntity;

class Page extends BaseController
{
    public function __construct($user)
    {
        parent::__construct($user);

    }

    public function getIndex($idOrSlug = null)
    {

        $page = PageEntity::get(["id" => $idOrSlug, "slug" => $idOrSlug], "OR");
        $data = [
            $pageContent = $page
        ];
        $this->render("page", $page->title, $data);
    }
}
