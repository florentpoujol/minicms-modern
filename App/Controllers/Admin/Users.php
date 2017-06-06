<?php

namespace App\Controllers\Admin;

use App\Entities\User;
use App\Messages;
use App\Route;
use App\Validate;

class Users extends AdminBaseController
{

    public function __construct($user)
    {
        parent::__construct($user);

        if ($this->user->isCommenter() && strpos(Route::$methodName, "Update") === false) {
            Route::redirect("admin/users/update/".$this->user->id);
        }
    }

    public function getRead($pageNumber = 1)
    {
        $allRows = User::getAll($pageNumber);

        $data = [
            "allRows" => $allRows,
            "pageNumber" => $pageNumber,
            "pagination" => [
                "itemsCount" => User::countAll(),
                "queryString" => Route::buildQueryString("admin/users/read/")
            ]
        ];
        $this->render("users.read", "users.pagetitle", $data);
    }

    public function getCreate()
    {
        if ($this->user->isWriter()) {
            Route::redirect("admin/users/read");
        }

        $this->render("users.update", "users.createnewuser", ["action" => "create"]);
    }

    public function postCreate()
    {
        if ($this->user->isWriter()) {
            Route::redirect("admin/users/read");
        }

        $post = [];
        if (Validate::csrf("usercreate")) {
            $post = Validate::sanitizePost([
                "id" => "int",
                "name" => "string",
                "email" => "string",
                "password" => "string",
                "password_confirmation" => "string",
                "role" => "string"
            ]);

            if (Validate::user($post)) {
                $user = User::create($post);

                if (is_object($user)) {
                    Messages::addSuccess("user.created");
                } else {
                    Messages::addError("db.createuser");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        $data = [
            "action" => "create",
            "post" => $post
        ];
        $this->render("users.update", "users.createnewuser", $data);
    }

    public function getUpdate($id)
    {
        if (! $this->user->isAdmin() && $id !== $this->user->id) {
            Route::redirect("admin/users/update/".$this->user->id);
        }

        $user = User::get(["id" => $id]);
        if ($user === false) {
            Messages::addError("user.unknown");
            Route::redirect("admin/users");
        }

        $data = [
            "action" => "update",
            "post" => $user->toArray()
        ];
        $this->render("users.update", "users.updateuser", $data);
    }

    public function postUpdate($id)
    {
        $post = [];
        if (Validate::csrf("userupdate")) {
            $post = Validate::sanitizePost([
                "id" => "int",
                "name" => "string",
                "email" => "string",
                "password" => "string",
                "password_confirmation" => "string",
                "role" => "string"
            ]);

            if (Validate::user($post)) {
                $user = User::get(["id" => $post["id"]]);

                if (is_object($user)) {
                    if ($user->update($post)) {
                        Messages::addSuccess("user.updated");
                        Route::redirect("admin/users/update/".$post["id"]);
                    } else {
                        Messages::addError("db.userupdated");
                    }
                } else {
                    Messages::addError("user.unknown");
                }
            }

        } else {
            Messages::addError("csrffail");
        }

        $data = [
            "action" => "update",
            "post" => $post
        ];
        $this->render("users.update", "users.createnewuser", $data);
    }

    public function postDelete()
    {
        if ($this->user->isAdmin()) {

            $id = (int)$_POST["user_id"];
            if ($this->user->id !== $id) {

                if (Validate::csrf("userdelete$id")) {

                    $user = User::get(["id" => $id]);
                    if (is_object($user)) {

                        if ($user->delete($this->user->id)) {
                            Messages::addSuccess("user.deleted");
                        } else {
                            Messages::addError("user.deleting");
                        }
                    } else {
                        Messages::addError("user.unknown");
                    }
                } else {
                    Messages::addError("csrffail");
                }
            } else {
                Messages::addError("user.cantdeleteownuser");
            }
        }

        Route::redirect("admin/users/read");
    }
}