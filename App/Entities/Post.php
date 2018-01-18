<?php

namespace App\Entities;

class Post extends BasePage
{
    public $user_id;
    public $category_id;

    /**
     * @return User|bool
     */
    public function getUser()
    {
        return User::get(["id" => $this->user_id]);
    }

    /**
     * @return Category|bool
     */
    public function getCategory()
    {
        return Category::get(["id" => $this->category_id]);
    }

    /**
     * @return Comment[]|bool
     */
    public function getComments()
    {
        if (is_int($this->id)) {
            return Comment::getAll(["post_id" => $this->id]);
        }
        return [];
    }

    public function getLink(string $routeName = "post")
    {
        return parent::getLink($routeName);
    }
}
