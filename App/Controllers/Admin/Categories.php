<?php

namespace App\Controllers\Admin;

use App\Entities\Category;
use App\Messages;
use App\Route;
use App\Validate;

class Categories extends AdminBaseController
{
    public function getRead($pageNumber = 1)
    {
        $allRows = Category::getAll(["pageNumber" => $pageNumber]);

        $data = [
            "allRows" => $allRows,
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => Category::countAll(),
                "queryString" => Route::buildQueryString("admin/categories/read")
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
        $post = Validate::sanitizePost([
            "slug" => "string",
            "title" => "string"
        ]);
        if (Validate::csrf("categorycreate")) {

            if (Validate::category($post)) {
                $cat = Category::create($post);

                if (is_object($cat)) {
                    Messages::addSuccess("categories.created");
                    Route::redirect("admin/categories/update/".$cat->id);
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

    public function getUpdate($id)
    {
        $cat = Category::get($id);
        if ($cat === false) {
            Messages::addError("category.unknown");
            Route::redirect("admin/categories/read");
        }

        $data = [
            "action" => "update",
            "post" => $cat->toArray()
        ];
        $this->render("categories.update", "categories.update", $data);
    }

    public function postUpdate()
    {
        $post = Validate::sanitizePost([
            "id" => "int",
            "slug" => "string",
            "title" => "string"
        ]);
        if (Validate::csrf("categoryupdate")) {

            if (Validate::category($post)) {
                $cat = Category::get($post["id"]);

                if (is_object($cat)) {
                    if ($cat->update($post)) {
                        Messages::addSuccess("category.updated");
                        Route::redirect("admin/categories/update/".$cat->id);
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
        if (Validate::csrf("categorydelete$id")) {
            $cat = Category::get($id);
            if (is_object($cat)) {
                if ($cat->delete()) {
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

        Route::redirect("admin/categories/read");
    }
}
