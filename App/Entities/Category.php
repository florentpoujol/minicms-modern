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
     * @param array $params
     * @return Post[]|bool
     */
    public function getPosts($params = [])
    {
        $params = array_merge(["category_id" => $this->id], $params);
        return Post::getAll($params);
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

    public function getLink($routeName = "category")
    {
        return parent::getLink($routeName);
    }
}
