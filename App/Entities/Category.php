<?php

namespace App\Entities;

class Category extends Entity
{
    public $slug;
    public $title;

    /**
     * @return string
     */
    protected static function getTableName()
    {
        return "categories";
    }

    /**
     * @return Category|false
     */
    public static function get($params, $condition = "AND")
    {
        // note: redeclaring a method like that seems necessary due to a probable bug
        // in PHPStorm that does not properly handle a return type  $this|bool on the parent method
        return parent::get($params, $condition);
    }

    /**
     * @return Category|false
     */
    public static function create($data)
    {
        // same reason as  for ::get()
        return parent::create($data);
    }

    /**
     * @return Post[]|bool
     */
    public function getPosts()
    {
        return Post::getAll(["category_id" => $this->id]);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $posts = $this->getPosts();
        foreach ($posts as $post) {
            $post->delete();
        }

        return parent::delete();
    }
}
