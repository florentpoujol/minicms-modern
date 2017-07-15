<?php

namespace App\Controllers\Admin;

use App\Entities\Page;
use App\Messages;
use App\Route;
use App\Validate;

class Pages extends AdminBaseController
{
    public function getRead($pageNumber = 1)
    {
        $allRows = Page::getAll(["pageNumber" => $pageNumber]);

        $data = [
            "allRows" => $allRows,
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => Page::countAll(),
                "queryString" => Route::buildQueryString("admin/pages/read")
            ]
        ];
        $this->render("pages.read", "admin.page.readtitle", $data);
    }

    public function getCreate()
    {
        $this->render("pages.update", "admin.page.create", ["action" => "create"]);
    }

    public function postCreate()
    {
        $post = Validate::sanitizePost([
            "id" => "int",
            "slug" => "string",
            "title" => "string",
            "content" => "string",
            "parent_page_id" => "int",
            "published" => "checkbox",
            "allow_comments" => "checkbox"
        ]);

        if (Validate::csrf("pagecreate")) {
            if (Validate::page($post)) {
                $page = Page::create($post);

                if (is_object($page)) {
                    Messages::addSuccess("page.created");
                    Route::redirect("admin/pages/update/".$page->id);
                } else {
                    Messages::addError("page.create");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        $data = [
            "action" => "create",
            "post" => $post
        ];
        $this->render("pages.update", "admin.page.create", $data);
    }

    public function getUpdate($id)
    {
        $page = Page::get($id);
        if ($page === false) {
            Messages::addError("page.unknown");
            Route::redirect("admin/pages/read");
        }

        $data = [
            "action" => "update",
            "post" => $page->toArray()
        ];
        $this->render("pages.update", "admin.page.updatetitle", $data);
    }

    public function postUpdate()
    {
        $post = Validate::sanitizePost([
            "id" => "int",
            "slug" => "string",
            "title" => "string",
            "content" => "string",
            "parent_page_id" => "int",
            "published" => "checkbox",
            "allow_comments" => "checkbox"
        ]);

        if (Validate::csrf("pageupdate")) {

            if (Validate::page($post)) {
                $page = Page::get($post["id"]);

                if (is_object($page)) {
                    if ($page->update($post)) {
                        Messages::addSuccess("page.updated");
                        Route::redirect("admin/pages/update/".$page->id);
                    } else {
                        Messages::addError("db.pageupdated");
                    }
                } else {
                    Messages::addError("page.unknown");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        $post["creation_datetime"] = Page::get($post["id"])->creation_datetime;

        $data = [
            "action" => "update",
            "post" => $post
        ];
        $this->render("pages.update", "admin.page.updatetitle", $data);
    }

    public function postDelete()
    {
        $id = (int)$_POST["id"];
        if (Validate::csrf("pagedelete$id")) {
            $page = Page::get($id);
            if (is_object($page)) {
                if ($page->delete()) {
                    Messages::addSuccess("page.deleted");
                } else {
                    Messages::addError("page.deleting");
                }
            } else {
                Messages::addError("page.unknown");
            }
        } else {
            Messages::addError("csrffail");
        }

        Route::redirect("admin/pages/read");
    }
}
