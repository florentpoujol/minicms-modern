<?php

namespace App\Controllers\Admin;

use App\Config;
use App\Form;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;
use App\Entities\Repositories\Category as CategoryRepo;

class Categories extends AdminBaseController
{
    /**
     * @var CategoryRepo
     */
    public $categoryRepo;

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config, Form $form,
        CategoryRepo $categoryRepo)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config, $form);
        $this->categoryRepo = $categoryRepo;
    }

    public function getRead(int $pageNumber = 1)
    {
        $allRows = $this->categoryRepo->getAll(["pageNumber" => $pageNumber]);

        $data = [
            "allRows" => $allRows,
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => $this->categoryRepo->countAll(),
                "queryString" => $this->router->getQueryString("admin/categories/read")
            ],
        ];
        $this->render("categories.read", $data);
    }

    public function getCreate()
    {
        $this->render("categories.update", [
            "action" => "create",
            "pageTitle" => "admin.categories.create",
        ]);
    }

    public function postCreate()
    {
        $post = $this->validator->sanitizePost([
            "slug" => "string",
            "title" => "string",
        ]);

        if ($this->validator->csrf("categorycreate")) {
            if ($this->validator->category($post)) {
                $category = $this->categoryRepo->create($post);

                if (is_object($category)) {
                    $this->session->addSuccess("categories.created");
                    $this->router->redirect("admin/categories/update/$category->id");
                    return;
                } else {
                    $this->session->addError("db.createcategory");
                }
            }
        } else {
            $this->session->addError("csrffail");
        }

        $data = [
            "action" => "create",
            "post" => $post,
            "pageTitle" => $this->lang->get("categories.createnewcategory"),
        ];
        $this->render("categories.update", $data);
    }

    public function getUpdate(int $categoryId)
    {
        $category = $this->categoryRepo->get($categoryId);
        if ($category === false) {
            $this->session->addError("category.unknown");
            $this->router->redirect("admin/categories/read");
            return;
        }

        $data = [
            "action" => "update",
            "post" => $category->toArray(),
        ];
        $this->render("categories.update", $data);
    }

    public function postUpdate(int $categoryId)
    {
        $post = $this->validator->sanitizePost([
            "slug" => "string",
            "title" => "string",
        ]);
        $post["id"] = $categoryId; // prevent the validator to check if the slug already exists

        if ($this->validator->csrf("categoryupdate")) {
            if ($this->validator->category($post)) {
                $category = $this->categoryRepo->get($categoryId);

                if (is_object($category)) {
                    if ($category->update($post)) {
                        $this->session->addSuccess("category.updated");
                        $this->router->redirect("admin/categories/update/$category->id");
                        return;
                    } else {
                        $this->session->addError("db.categoryupdated");
                    }
                } else {
                    $this->session->addError("category.unknown");
                }
            }
        } else {
            $this->session->addError("csrffail");
        }

        $data = [
            "action" => "update",
            "post" => $post,
        ];
        $this->render("categories.update", $data);
    }

    public function postDelete(int $categoryId)
    {
        if ($this->validator->csrf("categorydelete$categoryId")) {
            $category = $this->categoryRepo->get($categoryId);
            if (is_object($category)) {
                if ($category->delete()) {
                    $this->session->addSuccess("category.deleted");
                } else {
                    $this->session->addError("category.deleting");
                }
            } else {
                $this->session->addError("category.unknown");
            }
        } else {
            $this->session->addError("csrffail");
        }

        $this->router->redirect("admin/categories/read");
    }
}
