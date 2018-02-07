<?php

namespace App\Entities;

use App\Entities\Repositories\Post as PostRepo;
use App\Entities\Repositories\Comment as CommentRepo;
use App\Entities\Repositories\Category as CategoryRepo;
use App\Entities\Repositories\User as UserRepo;
use App\Router;

class Post extends BasePage
{
    use UserOwnedTrait;

    public $category_id = -1;

    /**
     * @var PostRepo
     */
    protected $postRepo;

    /**
     * @var CommentRepo
     */
    protected $commentRepo;

    /**
     * @var CategoryRepo
     */
    protected $categoryRepo;

    public function __construct(Router $router, PostRepo $postRepo, CommentRepo $commentRepo, CategoryRepo $categoryRepo, UserRepo $userRepo)
    {
        parent::__construct($router);
        $this->postRepo = $postRepo;
        $this->commentRepo = $commentRepo;
        $this->categoryRepo = $categoryRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * @return Category|false
     */
    public function getCategory()
    {
        return $this->categoryRepo->get(["id" => $this->category_id]);
    }

    /**
     * @return Comment[]|false
     */
    public function getComments()
    {
        return $this->commentRepo->getAll(["post_id" => $this->id]);
    }

    public function countComments(): int
    {
        return $this->commentRepo->countAll(["post_id" => $this->id]);
    }

    public function getLink(string $routeName = "post")
    {
        return parent::getLink($routeName);
    }
}
