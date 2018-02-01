<?php

namespace App\Entities;

use App\Entities\Repositories\Page as PageRepo;
use App\Entities\Repositories\Comment as CommentRepo;
use App\Entities\Repositories\User as UserRepo;
use App\Router;

class Page extends BasePage
{
    use UserOwnedTrait;

    public $parent_page_id = -1;

    /**
     * @var PageRepo
     */
    protected $pageRepo;

    /**
     * @var CommentRepo
     */
    protected $commentRepo;

    public function __construct(Router $router, PageRepo $pageRepo, CommentRepo $commentRepo, UserRepo $userRepo)
    {
        parent::__construct($router);
        $this->pageRepo = $pageRepo;
        $this->commentRepo = $commentRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * @return Page|bool|null
     */
    public function getParent()
    {
        if ($this->parent_page_id !== null) {
            return $this->pageRepo->get($this->parent_page_id);
        }
        return null;
    }

    /**
     * @return Page[]|false
     */
    public function getChildren()
    {
        return $this->pageRepo->getAll(["parent_page_id" => $this->id]);
    }

    /**
     * @return Comment[]|false
     */
    public function getComments()
    {
        return $this->commentRepo->getAll(["page_id" => $this->id]);
    }

    public function countComments(): int
    {
        return $this->commentRepo->countAll(["post_id" => $this->id]);
    }

    public function getLink(string $routeName = "page")
    {
        return parent::getLink($routeName);
    }
}
