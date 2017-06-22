<?php

namespace App\Entities;

class Post extends BasePage
{
    public $category_id;

    /**
     * @return Category|bool
     */
    public function getCategory()
    {
        return Category::get(["category_id" => $this->category_id]);
    }

    /**
     * @return Comment[]|bool
     */
    public function getComments()
    {
        return Comment::getAll(["post_id" => $this->id]);
    }
}
