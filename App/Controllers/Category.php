<?php

namespace App\Controllers;

use App\Entities\Repositories\Category as CategoryRepo;
use App\Entities\Repositories\Post as PostRepo;
use App\Config;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;

class Category extends BaseController
{
    /**
     * @var CategoryRepo
     */
    public $categoryRepo;

    /**
     * @var PostRepo
     */
    public $postRepo;

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config,
        CategoryRepo $categoryRepo, PostRepo $postRepo)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config);
        $this->categoryRepo = $categoryRepo;
        $this->postRepo = $postRepo;
    }

    public function getCategory(int $categoryId = -1, int $pageNumber = 1)
    {
        $category = $this->categoryRepo->get($categoryId);
        if ($category === false) {
            $this->session->addError("category.unknown");
            $this->router->redirect("blog");
            return;
        }

        $data = [
            "category" => $category,
            "posts" => $category->getPosts(["pageNumber" => $pageNumber]),
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => $this->postRepo->countAll(["category_id" => $categoryId]),
                "queryString" => $this->router->getQueryString("category/$categoryId")
            ],
            "pageTitle" => $this->lang->get("category.pagetitle", ["categoryTitle" => $category->title]),
        ];

        $this->render("category", $data);
    }
}
