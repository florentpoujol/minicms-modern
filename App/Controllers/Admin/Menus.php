<?php

namespace App\Controllers\Admin;

use App\Entities\Menu;
use App\Messages;
use App\Route;
use App\Validate;

class Menus extends AdminBaseController
{
    public function getRead($pageNumber = 1)
    {
        $allRows = Menu::getAll(["pageNumber" => $pageNumber]);

        $data = [
            "allRows" => $allRows,
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => Menu::countAll(),
                "queryString" => Route::buildQueryString("admin/menu/read")
            ]
        ];
        $this->render("menus.read", "admin.menu.readtitle", $data);
    }

    public function getCreate()
    {
        $data = [
            "action" => "create",
            "post" => ["structure" => []]
        ];
        $this->render("menus.update", "admin.menu.create", $data);
    }

    public function postCreate()
    {
        $post = Validate::sanitizePost([
            "id" => "int",
            "name" => "string",
            "in_use" => "checkbox",
            "structure" => "array",
            "json_structure" => "string"
        ]);

        if (Validate::csrf("menucreate")) {
            if (Validate::menu($post)) {
                $menu = Menu::create($post);

                if (is_object($menu)) {
                    Messages::addSuccess("menu.created");
                    Route::redirect("admin/menus/update/".$menu->id);
                } else {
                    Messages::addError("menu.create");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        $data = [
            "action" => "create",
            "post" => $post
        ];
        $this->render("menus.update", "admin.menu.create", $data);
    }

    public function getUpdate($id)
    {
        $menu = Menu::get($id);
        if ($menu === false) {
            Messages::addError("menu.unknown");
            Route::redirect("admin/menus/read");
        }

        $data = [
            "action" => "update",
            "post" => $menu->toArray()
        ];
        $this->render("menus.update", "admin.menu.updatetitle", $data);
    }

    public function postUpdate()
    {
        $post = Validate::sanitizePost([
            "id" => "int",
            "name" => "string",
            "in_use" => "checkbox",
            "structure" => "array",
            "json_structure" => "string"
        ]);

        if (Validate::csrf("menuupdate")) {

            if (Validate::menu($post)) {
                $menu = Menu::get($post["id"]);

                if (is_object($menu)) {
                    if ($menu->update($post)) {
                        Messages::addSuccess("menu.updated");
                        Route::redirect("admin/menus/update/".$menu->id);
                    } else {
                        Messages::addError("db.pageupdated");
                    }
                } else {
                    Messages::addError("menu.unknown");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        $post["creation_datetime"] = Menu::get($post["id"])->creation_datetime;

        $data = [
            "action" => "update",
            "post" => $post
        ];
        $this->render("menus.update", "admin.menu.updatetitle", $data);
    }

    public function postDelete()
    {
        $id = (int)$_POST["id"];
        if (Validate::csrf("menudelete$id")) {
            $menu = Menu::get($id);
            if (is_object($menu)) {
                if ($menu->delete()) {
                    Messages::addSuccess("menu.deleted");
                } else {
                    Messages::addError("menu.deleting");
                }
            } else {
                Messages::addError("menu.unknown");
            }
        } else {
            Messages::addError("csrffail");
        }

        Route::redirect("admin/menus/read");
    }
}
