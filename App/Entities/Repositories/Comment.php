<?php

namespace App\Entities\Repositories;

use App\Entities\Comment as CommentEntity;
use App\Entities\User as UserEntity;

class Comment extends Entity
{
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
     * Get all comments that user owns, plus the comments attached to the pages or posts he owns
     * @return CommentEntity[]|false
     */
    public function getAllForEditor(UserEntity $user, int $pageNumber = null)
    {
        $builder = $this->database->getQueryBuilder()
            ->select("comments.*")->fromTable("comments")
            ->leftJoin("posts")->on("comments.post_id = posts.id")
            ->leftJoin("pages")->on("comments.page_id = pages.id")
            ->where("comments.user_id = :user_id")
            ->orWhere("posts.user_id = :post_user_id")
            ->orWhere("pages.user_id = :page_user_id")
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
            $results = [];
            foreach ($query->fetchAll() as $result) {
                $results[] = $this->entityClassName::createHydrated($result);
            }
            return $results;
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
