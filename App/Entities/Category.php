<?php

namespace App\Entities;

class Category extends Entity
{
    public $slug;
    public $name;

    /**
     * @return string
     */
    protected static function getTableName()
    {
        return "categories";
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
