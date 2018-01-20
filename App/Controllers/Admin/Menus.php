<?php

namespace App\Controllers\Admin;

use App\Entities\Menu;
use App\Messages;
use App\Router;
use App\Validator;

class Menus extends AdminBaseController
{
    public function getRead(int $pageNumber = 1)
    {
        $allRows = Menu::getAll(["pageNumber" => $pageNumber]);

        $data = [
            "allRows" => $allRows,
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => Menu::countAll(),
                "queryString" => Router::getQueryString("admin/menu/read")
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
        $post = Validator::sanitizePost([
            "id" => "int",
            "name" => "string",
            "in_use" => "checkbox",
            "structure" => "array",
            "json_structure" => "string"
        ]);

        if (Validator::csrf("menucreate")) {
            if (Validator::menu($post)) {
                $menu = Menu::create($post);

                if (is_object($menu)) {
                    Messages::addSuccess("menu.created");
                    Router::redirect("admin/menus/update/$menu->id");
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

    public function getUpdate(int $menuId)
    {
        $menu = Menu::get($menuId);
        if ($menu === false) {
            Messages::addError("menu.unknown");
            Router::redirect("admin/menus/read");
        }

        $data = [
            "action" => "update",
            "post" => $menu->toArray()
        ];
        $this->render("menus.update", "admin.menu.updatetitle", $data);
    }

    public function postUpdate()
    {
        $post = Validator::sanitizePost([
            "id" => "int",
            "name" => "string",
            "in_use" => "checkbox",
            "structure" => "array",
            "json_structure" => "string"
        ]);

        if (Validator::csrf("menuupdate")) {

            if (Validator::menu($post)) {
                $menu = Menu::get($post["id"]);

                if (is_object($menu)) {
                    if ($menu->update($post)) {
                        Messages::addSuccess("menu.updated");
                        Router::redirect("admin/menus/update/$menu->id");
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
        if (Validator::csrf("menudelete$id")) {
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

        Router::redirect("admin/menus/read");
    }
}
