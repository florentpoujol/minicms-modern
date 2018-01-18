<?php

namespace App\Entities;

class Category extends Entity
{
    public $slug;
    public $title;

    protected static function getTableName(): string
    {
        return "categories";
    }

    /**
     * @return Post[]|bool
     */
    public function getPosts(array $whereConditions = [])
    {
        $whereConditions = array_merge(["category_id" => $this->id], $whereConditions);
        return Post::getAll($whereConditions);
    }

    public function delete(): bool
    {
        $posts = $this->getPosts();
        foreach ($posts as $post) {
            $post->delete();
        }
        return parent::delete();
    }

    public function getLink(string $routeName = "category")
    {
        return parent::getLink($routeName);
    }
}
