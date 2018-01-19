<?php

namespace App\Entities\Repositories;

use App\Config;
use App\Database;
use App\Entities\Post as PostEntity;
use App\Entities\Page as PageEntity;
use App\Entities\Comment as CommentEntity;
use App\Entities\User as UserEntity;
use PDO;

class Comment extends Entity
{
    /**
     * @var User
     */
    public $userRepo;

    /**
     * @var Post
     */
    public $postRepo;

    /**
     * @var Page
     */
    public $pageRepo;

    /**
     * @return CommentEntity|false
     */
    public function get($whereConditions, bool $useWhereOrOperator = false)
    {
        return parent::get($whereConditions, $useWhereOrOperator);
    }

    /**
     * @return CommentEntity[]|false
     */
    public function getAll(array $params = [])
    {
        return parent::getAll($params);
    }

    /**
     * @return UserEntity|bool
     */
    public function getUser(CommentEntity $comment)
    {
        return $this->userRepo->get($comment->user_id);
    }

    /**
     * @return PostEntity|bool
     */
    public function getPost(CommentEntity $comment)
    {
        return $this->postRepo->get($comment->post_id);
    }

    /**
     * @return PageEntity|bool
     */
    public function getPage(CommentEntity $comment)
    {
        return $this->pageRepo->get($comment->page_id);
    }

    /**
     * Get all comments that user owns, plus the comments attached to the pages or posts he owns
     * @return CommentEntity[]|false
     */
    public function getAllForEditor(UserEntity $user, int $pageNumber = null)
    {
        $builder = $this->database->getQueryBuilder()
            ->select("comments.*")->fromTable("comments")
            ->join("posts")->on("comments.post_id = posts.id")
            ->join("pages")->on("comments.page_id = pages.id")
            ->where("comments.user_id = :user_id")
            ->orWhere("posts.user_id = :post_user_id")
            ->orWhere("posts.user_id = :post_user_id")
            ->orderBy("comments.id");

        if ($pageNumber !== null) {
            $pageNumber = $pageNumber - 1;
            if ($pageNumber < 0) {
                $pageNumber = 0;
            }
            $itemsPerPage = $this->config->get("items_per_page");
            $builder->limit($itemsPerPage)->offset($pageNumber * $itemsPerPage);
        }

        $query = $builder->execute([
            "user_id" => $user->id,
            "post_user_id" => $user->id,
            "page_user_id" => $user->id,
        ]);

        if ($query !== false) {
            $query->setFetchMode(PDO::FETCH_CLASS, CommentEntity::class);
            return $query->fetchAll();
        }
        return false;
    }

    /**
     * @return CommentEntity|false
     */
    public function create(array $data)
    {
        if (!isset($data["post_id"])) {
            $data["post_id"] = null;
        }
        if (!isset($data["page_id"])) {
            $data["page_id"] = null;
        }
        return parent::create($data);
    }
}
