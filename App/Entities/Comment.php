<?php

namespace App\Entities;

class Comment extends Entity
{
    public $content;
    public $user_id;
    public $post_id;
    public $page_id;

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

    /**
     * @param array $newComment
     * @return Comment|bool
     */
    public static function create($newComment)
    {
        if (! isset($newComment["post_id"])) {
            $newComment["post_id"] = null;
        }

        if (! isset($newComment["page_id"])) {
            $newComment["page_id"] = null;
        }

        return parent::create($newComment);
    }
}
