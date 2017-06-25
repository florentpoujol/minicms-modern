<?php

namespace App\Entities;

class Comment extends Entity
{
    public $content;
    public $user_id;
    public $post_id;
    public $page_id;

    /**
     * @return Comment|false
     */
    public static function get($params, $condition = "AND")
    {
        // note: redeclaring a method like that seems necessary due to a probable bug
        // in PHPStorm that does not properly handle a return type  $this|bool on the parent method
        return parent::get($params, $condition);
    }

    /**
     * @param array $data
     * @return Comment|bool
     */
    public static function create($data)
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
