<?php

namespace App\Controllers;

use App\Entities\Repositories\Category as CategoryRepo;
use App\Entities\Repositories\Post as PostRepo;
use App\Lang;
use App\Renderer;
use App\Route;
use App\Session;
use App\Validator;
use StdCmp\Router\Router;

class Blog extends BaseController
{
    /**
     * @var PostRepo
     */
    public $potRepo;

    /**
     * @var CategoryRepo
     */
    public $categoryRepo;

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer,
        PostRepo $potRepo, CategoryRepo $categoryRepo
    )
    {
        parent::__construct($lang, $session, $validator, $router, $renderer);
        $this->potRepo = $potRepo;
        $this->categoryRepo = $categoryRepo;
    }

    public function getBlog(int $pageNumber = 1)
    {
        $data = [
            "posts" => $this->potRepo->getAll(["pageNumber" => $pageNumber]),
            "categories" => $this->categoryRepo->getAll(),
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => $this->potRepo->countAll(),
                // "queryString" => Route::buildQueryString("blog")
                "queryString" => ""
            ]
        ];

        $this->render("blog", "Blog", $data);
    }
}
