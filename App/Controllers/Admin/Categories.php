<?php

namespace App\Controllers\Admin;

use App\Entities\Category;
use App\Router;
use App\Validator;

class Categories extends AdminBaseController
{
    public function getRead(int $pageNumber = 1)
    {
        $allRows = Category::getAll(["pageNumber" => $pageNumber]);

        $data = [
            "allRows" => $allRows,
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => Category::countAll(),
                "queryString" => Router::getQueryString("admin/categories/read")
            ]
        ];
        $this->render("categories.read", "categories.pagetitle", $data);
    }

    public function getCreate()
    {
        $this->render("categories.update", "categories.createnewcategory", ["action" => "create"]);
    }

    public function postCreate()
    {
        $post = Validator::sanitizePost([
            "slug" => "string",
            "title" => "string"
        ]);
        if (Validator::csrf("categorycreate")) {

            if (Validator::category($post)) {
                $cat = Category::create($post);

                if (is_object($cat)) {
                    Messages::addSuccess("categories.created");
                    Router::redirect("admin/categories/update/".$cat->id);
                } else {
                    Messages::addError("db.createcategory");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        $data = [
            "action" => "create",
            "post" => $post
        ];
        $this->render("categories.update", "categories.createnewcategory", $data);
    }

    public function getUpdate(int $categoryId)
    {
        $category = Category::get($categoryId);
        if ($category === false) {
            Messages::addError("category.unknown");
            Router::redirect("admin/categories/read");
        }

        $data = [
            "action" => "update",
            "post" => $category->toArray()
        ];
        $this->render("categories.update", "categories.update", $data);
    }

    public function postUpdate()
    {
        $post = Validator::sanitizePost([
            "id" => "int",
            "slug" => "string",
            "title" => "string"
        ]);
        if (Validator::csrf("categoryupdate")) {

            if (Validator::category($post)) {
                $category = Category::get($post["id"]);

                if (is_object($category)) {
                    if ($category->update($post)) {
                        Messages::addSuccess("category.updated");
                        Router::redirect("admin/categories/update/".$category->id);
                    } else {
                        Messages::addError("db.categoryupdated");
                    }
                } else {
                    Messages::addError("category.unknown");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        $data = [
            "action" => "update",
            "post" => $post
        ];
        $this->render("categories.update", "categories.update", $data);
    }

    public function postDelete()
    {
        $id = (int)$_POST["id"];
        if (Validator::csrf("categorydelete$id")) {
            $category = Category::get($id);
            if (is_object($category)) {
                if ($category->delete()) {
                    Messages::addSuccess("category.deleted");
                } else {
                    Messages::addError("category.deleting");
                }
            } else {
                Messages::addError("category.unknown");
            }
        } else {
            Messages::addError("csrffail");
        }

        Router::redirect("admin/categories/read");
    }
}
