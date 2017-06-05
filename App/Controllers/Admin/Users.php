<?php

namespace App\Controllers\Admin;

use App\Entities\User;
use App\Route;

class Users extends AdminBaseController
{
    public function getRead($pageNumber = 1)
    {
        $allUsers = User::getAll($pageNumber);
        $data = [
            "allUsers" => $allUsers,
            "pageNumber" => $pageNumber,
            "paginationItemsCount" => User::countAll(),
            "paginationTarget" => Route::buildQueryString("admin/users/read/"),
        ];
        $this->render("users.read", "users.pagetitle", $data);
    }

    public function getCreate()
    {

    }

    public function postCreate()
    {

    }

    public function getUpdate($id)
    {

    }

    public function postUpdate()
    {

    }

    public function postDelete()
    {

    }
}