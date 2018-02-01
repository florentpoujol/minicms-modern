<?php

namespace App\Controllers\Admin;

use App\Config;
use App\Form;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;
use App\Entities\Repositories\Menu as MenuRepo;

class Menus extends AdminBaseController
{
    /**
     * @var MenuRepo
     */
    protected $menuRepo;

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config, Form $form,
        MenuRepo $menuRepo)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config, $form);
        $this->menuRepo = $menuRepo;
    }

    public function getRead(int $pageNumber = 1)
    {
        $allRows = $this->menuRepo->getAll(["pageNumber" => $pageNumber]);

        $data = [
            "allRows" => $allRows,
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => $this->menuRepo->countAll(),
                "queryString" => $this->router->getQueryString("admin/menu/read")
            ],
        ];
        $this->render("menus.read", $data);
    }

    public function getCreate()
    {
        $data = [
            "action" => "create",
            "post" => ["structure" => []],
        ];
        $this->render("menus.update", $data);
    }

    public function postCreate()
    {
        $post = $this->validator->sanitizePost([
            "title" => "string",
            "in_use" => "checkbox",
            "structure" => "array",
            "json_structure" => "string",
        ]);

        if ($this->validator->csrf("menucreate")) {
            if ($this->validator->menu($post)) {
                $menu = $this->menuRepo->create($post);

                if (is_object($menu)) {
                    $this->session->addSuccess("menu.created");
                    $this->router->redirect("admin/menus/update/$menu->id");
                    return;
                } else {
                    $this->session->addError("menu.create");
                }
            }
        } else {
            $this->session->addError("csrffail");
        }

        $data = [
            "action" => "create",
            "post" => $post,
        ];
        $this->render("menus.update", $data);
    }

    public function getUpdate(int $menuId)
    {
        $menu = $this->menuRepo->get($menuId);
        if ($menu === false) {
            $this->session->addError("menu.unknown");
            $this->router->redirect("admin/menus/read");
            return;
        }

        $data = [
            "action" => "update",
            "post" => $menu->toArray(),
        ];
        $this->render("menus.update", $data);
    }

    public function postUpdate(int $menuId)
    {
        $post = $this->validator->sanitizePost([
            "title" => "string",
            "in_use" => "checkbox",
            "structure" => "array",
            "json_structure" => "string"
        ]);
        $post["id"] = $menuId;

        if ($this->validator->csrf("menuupdate")) {
            if ($this->validator->menu($post)) {
                $menu = $this->menuRepo->get($post["id"]);

                if (is_object($menu)) {
                    if ($menu->update($post)) {
                        $this->session->addSuccess("menu.updated");
                        $this->router->redirect("admin/menus/update/$menu->id");
                        return;
                    } else {
                        $this->session->addError("db.pageupdated");
                    }
                } else {
                    $this->session->addError("menu.unknown");
                }
            }
        } else {
            $this->session->addError("csrffail");
        }

        $post["creation_datetime"] = $this->menuRepo->get($post["id"])->creation_datetime;

        $data = [
            "action" => "update",
            "post" => $post,
        ];
        $this->render("menus.update", $data);
    }

    public function postDelete(int $menuId)
    {
        if ($this->validator->csrf("menudelete$menuId")) {
            $menu = $this->menuRepo->get($menuId);
            if (is_object($menu)) {
                if ($menu->delete()) {
                    $this->session->addSuccess("menu.deleted");
                } else {
                    $this->session->addError("menu.deleting");
                }
            } else {
                $this->session->addError("menu.unknown");
            }
        } else {
            $this->session->addError("csrffail");
        }

        $this->router->redirect("admin/menus/read");
    }
}
