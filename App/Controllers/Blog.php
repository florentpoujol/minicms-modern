<?php

namespace App\Controllers;

use App\Entities\Category;
use App\Entities\Post;
use App\Route;

class Blog extends BaseController
{
    public function getBlog($pageNumber = 1)
    {
        $data = [
            "posts" => Post::getAll(["pageNumber" => $pageNumber]),
            "categories" => Category::getAll(),
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => Post::countAll(),
                "queryString" => Route::buildQueryString("blog")
            ]
        ];
        $this->render("blog", "Blog", $data);
    }
}
