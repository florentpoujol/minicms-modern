<?php

namespace App\Controllers;

use App\Config;
use App\Entities\Repositories\Category as CategoryRepo;
use App\Entities\Repositories\Post as PostRepo;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;

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
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config,
        PostRepo $potRepo, CategoryRepo $categoryRepo
    ) {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config);
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
                "queryString" => $this->router->getQueryString("blog"),
            ]
        ];

        $this->render("blog", $data);
    }
}
