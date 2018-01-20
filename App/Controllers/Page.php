<?php

namespace App\Controllers;

use App\Entities\Page as PageEntity;
use App\Messages;
use App\Router;

class Page extends Commentable
{
    public function getPage(int $pageId)
    {
        $page = PageEntity::get($pageId);

        if ($page === false) {
            Messages::addError("page.unknow");
            Router::redirect("blog");
        }

        $data = [
            "page" => $page
        ];
        $this->render("page", $page->title, $data);
    }

    public function postPage(int $pageId)
    {
        if (! isset($this->user)) {
            Messages::addError("user.mustbeloggedintopostcomment");
            Router::redirect("page/$pageId");
        }

        $page = PageEntity::get($pageId);

        if ($page === false) {
            Messages::addError("page.unknow");
            Router::redirect("blog");
        }

        $data = [
            "page" => $page,
            "commentPost" => $this->postComment($pageId)
        ];
        $this->render("page", $page->title, $data);
    }
}
