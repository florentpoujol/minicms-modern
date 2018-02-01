<?php

namespace App\Entities;

use App\Entities\Repositories\Comment as CommentRepo;
use App\Entities\Repositories\Page as PageRepo;
use App\Entities\Repositories\Post as PostRepo;
use App\Entities\Repositories\User as UserRepo;
use App\Router;

class Comment extends Entity
{
    use UserOwnedTrait;

    public $content = "";
    public $user_id = -1;
    public $post_id = -1;
    public $page_id = -1;

    /**
     * @var CommentRepo
     */
    protected $commentRepo;

    /**
     * @var PageRepo
     */
    protected $pageRepo;

    /**
     * @var PostRepo
     */
    protected $postRepo;

    public function __construct(Router $router, CommentRepo $commentRepo, PageRepo $pageRepo, PostRepo $postRepo, UserRepo $userRepo)
    {
        parent::__construct($router);
        $this->commentRepo = $commentRepo;
        $this->pageRepo = $pageRepo;
        $this->postRepo = $postRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * @return Post|false
     */
    public function getPost()
    {
        return $this->postRepo->get(["id" => $this->post_id]);
    }

    /**
     * @return Page|false
     */
    public function getPage()
    {
        return $this->pageRepo->get(["id" => $this->page_id]);
    }

    public function getExcerpt(int $characterCount = 500): string
    {
        if ($characterCount <= 0) {
            $characterCount = 500;
        }
        return substr($this->content, 0, $characterCount);
    }

    public function canBeEditedByUser(User $user): bool
    {
        if ($this->user_id === $user->id || $user->isAdmin()) {
            return true;
        }
        if ($user->isWriter()) {
            $postOrPage = $this->getPost() ?: $this->getPage();
            return $postOrPage->user_id === $user->id;
        }
        return false;
    }
}
