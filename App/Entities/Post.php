<?php

namespace App\Entities;

class Post extends BasePage
{
    public $category_id;

    /**
     * @return Post|false
     */
    public static function get($params, $condition = "AND")
    {
        // note: redeclaring a method like that seems necessary due to a probable bug
        // in PHPStorm that does not properly handle a return type  $this|bool on the parent method
        return parent::get($params, $condition);
    }

    /**
     * @return Post|false
     */
    public static function create($data)
    {
        // same reason as  for ::get()
        return parent::create($data);
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
    }
}
