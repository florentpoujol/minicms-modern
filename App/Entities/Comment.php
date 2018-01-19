<?php

namespace App\Entities;

class Comment extends Entity
{
    public $content;
    public $user_id;
    public $post_id;
    public $page_id;

    /**
     * @return Comment[]|false
     */
    public static function getAllForWriter(int $id, int $pageNumber = null)
    {
        $limit = "";
        if ($pageNumber !== null) {
            $pageNumber = $pageNumber - 1;
            if ($pageNumber < 0) {
                $pageNumber = 0;
            }
            $itemsPerPage = self::$config->get("items_per_page");

            $offset = $pageNumber * $itemsPerPage;
            $limit = " LIMIT $offset, $itemsPerPage";
        }
        $orderBy = " ORDER BY comments.id ASC ";

        $strQuery = "SELECT comments.* FROM comments 
        LEFT JOIN posts ON comments.post_id = posts.id
        WHERE comments.user_id = :user_id OR posts.user_id = :post_user_id";

        $query = self::$db->prepare($strQuery.$orderBy.$limit);
        $query->setFetchMode(\PDO::FETCH_CLASS, get_called_class());
        $success = $query->execute([
            "user_id" => $id,
            "post_user_id" => $id,
        ]);

        if ($success) {
            return $query->fetchAll();
        }
        return false;
    }

    /**
     * @return Comment|bool
     */
    public static function create(array $data)
    {
        if (! isset($data["post_id"])) {
            $data["post_id"] = null;
        }

        if (! isset($data["page_id"])) {
            $data["page_id"] = null;
        }

        return parent::create($data);
    }

    /**
     * @return User|bool
     */
    public function getUser()
    {
        return User::get(["id" => $this->user_id]);
    }

    /**
     * @return Post|bool
     */
    public function getPost()
    {
        return Post::get(["id" => $this->post_id]);
    }

    /**
     * @return Page|bool
     */
    public function getPage()
    {
        return Page::get(["id" => $this->page_id]);
    }
}
