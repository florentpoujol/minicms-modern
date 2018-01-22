<?php

namespace App\Entities;

use App\Entities\Repositories\Post as PostRepo;
use App\Entities\Repositories\Page as PageRepo;
use App\Entities\Repositories\Comment as CommentRepo;
use App\Entities\Repositories\Media as MediaRepo;
use App\Entities\Repositories\User as UserRepo;
use App\Router;

class User extends Entity
{
    public $name = "";
    public $email = "";
    public $email_token = "";
    public $password_hash = "";
    public $password_token = "";
    public $password_change_time = -1;
    public $role = "";
    public $is_blocked = -1;

    /**
     * @var UserRepo
     */
    protected $userRepo;

    /**
     * @var PageRepo
     */
    protected $pageRepo;

    /**
     * @var PostRepo
     */
    protected $postRepo;

    /**
     * @var CommentRepo
     */
    protected $commentRepo;

    /**
     * @var MediaRepo
     */
    protected $mediaRepo;

    public function __construct(Router $router, UserRepo $userRepo, PostRepo $postRepo, PageRepo $pageRepo, CommentRepo $commentRepo, MediaRepo $mediaRepo)
    {
        parent::__construct($router);
        $this->userRepo = $userRepo;
        $this->postRepo = $postRepo;
        $this->pageRepo = $pageRepo;
        $this->commentRepo = $commentRepo;
        $this->mediaRepo = $mediaRepo;
    }

    /**
     * @return Page[]|false
     */
    public function getPages()
    {
        return $this->pageRepo->getAll(["user_id" => $this->id]);
    }

    /**
     * @return Post[]|false
     */
    public function getPosts()
    {
        return $this->postRepo->getAll(["user_id" => $this->id]);
    }

    /**
     * @return Comment[]|false
     */
    public function getComments()
    {
        return $this->commentRepo->getAll(["user_id" => $this->id]);
    }

    /**
     * @return Media[]|false
     */
    public function getMedias()
    {
        return $this->mediaRepo->getAll(["user_id" => $this->id]);
    }

    public function updatePasswordToken(string $token): bool
    {
        $data = [
            "password_token" => $token,
            "password_change_time" => $token !== "" ? time() : 0,
        ];
        return parent::update($data);
    }

    public function updatePassword(string $password): bool
    {
        $data = [
            "password_token" => "",
            "password_change_time" => 0,
            "password_hash" => password_hash($password, PASSWORD_DEFAULT)
        ];
        return parent::update($data);
    }

    public function updateEmailToken(string $token): bool
    {
        if ($this->userRepo->update($this, ["email_token" => $token])) {
            $this->email_token = $token;
            return true;
        }
        return false;
    }

    public function block(bool $block = true): bool
    {
        $block = (int)$block;
        if ($this->userRepo->update($this, ["is_blocked" => $block])) {
            $this->is_blocked = $block;
            return true;
        }
        return false;
    }

    public function delete()
    {
        throw new \LogicException('Users can only be deleted by admins. Please call deleteByAdmin($adminId) instead.');
    }

    public function deleteByAdmin(int $adminUserId): bool
    {
        if ($this->userRepo->deleteByAdmin($this, $adminUserId)) {
            $this->isDeleted = true;
            return true;
        }
        return false;
    }

    public function isAdmin(): bool
    {
        return $this->role === "admin";
    }

    public function isWriter(): bool
    {
        return $this->role === "writer";
    }

    public function isCommenter(): bool
    {
        return $this->role === "commenter";
    }

    public function isBlocked(): bool
    {
        return $this->is_blocked === 1;
    }
}
