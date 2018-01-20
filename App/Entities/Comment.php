<?php

namespace App\Entities;

use App\Entities\Repositories\Comment as CommentRepo;
use App\Entities\Repositories\Page as PageRepo;
use App\Entities\Repositories\Post as PostRepo;
use App\Entities\Repositories\User as UserRepo;

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
    public $commentRepo;

    /**
     * @var PageRepo
     */
    public $pageRepo;

    /**
     * @var PostRepo
     */
    public $postRepo;

    public function __construct(CommentRepo $commentRepo, PageRepo $pageRepo, PostRepo $postRepo, UserRepo $userRepo)
    {
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
}
