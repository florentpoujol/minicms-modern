<?php

namespace App\Controllers;

use App\Entities\Page as PageEntity;
use App\Messages;
use App\Route;

class Page extends BaseController
{
    public function getPage($pageId)
    {
        $page = PageEntity::get($pageId);

        if ($page === false) {
            Messages::addError("post.unknow");
            Route::redirect("blog");
        }

        $data = [
            "page" => $page
        ];
        $this->render("page", $page->title, $data);
    }
}
