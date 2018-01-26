<?php

namespace App\Controllers;

use App\Entities\Repositories\Comment as CommentRepo;
use App\Entities\Repositories\Page as PageRepo;
use App\Config;
use App\Form;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;

class Page extends Commentable
{
    /**
     * @var PageRepo
     */
    public $pageRepo;

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config, CommentRepo $commentRepo,
        PageRepo $pageRepo, Form $form)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config, $commentRepo, $form);
        $this->pageRepo = $pageRepo;
    }

    public function getPage(int $pageId)
    {
        $page = $this->pageRepo->get($pageId);
        if ($page === false) {
            $this->session->addError("page.unknown");
            $this->router->redirect("blog");
            return;
        }

        $data = [
            "page" => $page,
            "pageTitle" => $page->title,
        ];
        $this->render("page", $data);
    }

    public function postPage(int $pageId)
    {
        if (! isset($this->user)) {
            $this->session->addError("user.mustbeloggedintopostcomment");
            $this->router->redirect("page/$pageId");
            return;
        }

        $page = $this->pageRepo->get($pageId);
        if ($page === false) {
            $this->session->addError("page.unknown");
            $this->router->redirect("blog");
            return;
        }

        $data = [
            "page" => $page,
            "pageTitle" => $page->title,
            "commentPost" => $this->postComment($pageId) // infos from the comment form, or nothing if creating comment is successful or form was empty
            // used to populate the form if needed
        ];
        $this->render("page", $data);
    }
}
