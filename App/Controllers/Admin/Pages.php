<?php

namespace App\Controllers\Admin;

use App\Config;
use App\Form;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;
use App\Entities\Repositories\Page as PageRepo;
use App\Entities\Repositories\User as UserRepo;

class Pages extends AdminBaseController
{
    /**
     * @var PageRepo
     */
    protected $pageRepo;

    /**
     * @var UserRepo
     */
    protected $userRepo;

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config, Form $form,
        PageRepo $pageRepo, UserRepo $userRepo)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config, $form);
        $this->pageRepo = $pageRepo;
        $this->userRepo = $userRepo;
    }

    public function getRead(int $pageNumber = 1)
    {
        $allRows = $this->pageRepo->getAll(["pageNumber" => $pageNumber]);

        $data = [
            "allRows" => $allRows,
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => $this->pageRepo->countAll(),
                "queryString" => $this->router->getQueryString("admin/pages/read")
            ],
        ];
        $this->render("pages.read", $data);
    }

    public function getCreate()
    {
        $this->render("pages.update", [
            "action" => "create",
            "parentPages" => $this->pageRepo->getAll(["parent_page_id" => null]),
            "users" => array_merge(
                $this->userRepo->getAll(["role" => "admin"]),
                $this->userRepo->getAll(["role" => "writer"])
            ),
        ]);
    }

    public function postCreate()
    {
        $post = $this->validator->sanitizePost([
            "slug" => "string",
            "title" => "string",
            "content" => "string",
            "parent_page_id" => "int",
            "user_id" => "int",
            "published" => "checkbox",
            "allow_comments" => "checkbox",
        ]);

        if ($this->validator->csrf("pagecreate")) {
            if ($this->validator->page($post)) {
                $page = $this->pageRepo->create($post);

                if (is_object($page)) {
                    $this->session->addSuccess("page.created");
                    $this->router->redirect("admin/pages/update/$page->id");
                    return;
                } else {
                    $this->session->addError("page.create");
                }
            }
        } else {
            $this->session->addError("csrffail");
        }

        $data = [
            "action" => "create",
            "post" => $post,
            "parentPages" => $this->pageRepo->getAll(["parent_page_id" => null]),
            "users" => array_merge(
                $this->userRepo->getAll(["role" => "admin"]),
                $this->userRepo->getAll(["role" => "writer"])
            ),
        ];
        $this->render("pages.update", $data);
    }

    public function getUpdate(int $pageId)
    {
        $page = $this->pageRepo->get($pageId);
        if ($page === false) {
            $this->session->addError("page.unknown");
            $this->router->redirect("admin/pages/read");
            return;
        }

        $data = [
            "action" => "update",
            "post" => $page->toArray(),
            "parentPages" => $this->pageRepo->getAll(["parent_page_id" => null]),
            "users" => array_merge(
                $this->userRepo->getAll(["role" => "admin"]),
                $this->userRepo->getAll(["role" => "writer"])
            ),
        ];
        $this->render("pages.update", $data);
    }

    public function postUpdate(int $pageId)
    {
        $post = $this->validator->sanitizePost([
            "id" => "int",
            "slug" => "string",
            "title" => "string",
            "content" => "string",
            "parent_page_id" => "int",
            "user_id" => "int",
            "published" => "checkbox",
            "allow_comments" => "checkbox",
        ]);
        $post["id"] = $pageId;

        if ($this->validator->csrf("pageupdate")) {
            if ($this->validator->page($post)) {
                $page = $this->pageRepo->get($post["id"]);

                if (is_object($page)) {
                    if ($page->update($post)) {
                        $this->session->addSuccess("page.updated");
                        $this->router->redirect("admin/pages/update/$page->id");
                        return;
                    } else {
                        $this->session->addError("db.pageupdated");
                    }
                } else {
                    $this->session->addError("page.unknown");
                }
            }
        } else {
            $this->session->addError("csrffail");
        }

        $post["creation_datetime"] = $this->pageRepo->get($post["id"])->creation_datetime;

        $data = [
            "action" => "update",
            "post" => $post,
            "parentPages" => $this->pageRepo->getAll(["parent_page_id" => null]),
            "users" => array_merge(
                $this->userRepo->getAll(["role" => "admin"]),
                $this->userRepo->getAll(["role" => "writer"])
            ),
        ];
        $this->render("pages.update", $data);
    }

    public function postDelete(int $pageId)
    {
        if ($this->validator->csrf("pagedelete$pageId")) {
            $page = $this->pageRepo->get($pageId);
            if (is_object($page)) {
                if ($page->delete()) {
                    $this->session->addSuccess("page.deleted");
                } else {
                    $this->session->addError("page.deleting");
                }
            } else {
                $this->session->addError("page.unknown");
            }
        } else {
            $this->session->addError("csrffail");
        }

        $this->router->redirect("admin/pages/read");
    }
}
