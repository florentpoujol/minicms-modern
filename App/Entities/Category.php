<?php

namespace App\Entities;

use App\Entities\Repositories\Category as CategoryRepo;
use App\Entities\Repositories\Post as PostRepo;

class Category extends Entity
{
    public $slug = "";

    /**
     * @var CategoryRepo
     */
    public $categoryRepo;

    /**
     * @var PostRepo
     */
    public $postRepo;

    public function __construct(CategoryRepo $categoryRepo, PostRepo $postRepo)
    {
        $this->categoryRepo = $categoryRepo;
        $this->postRepo = $postRepo;
    }

    /**
     * @return PostRepo[]|false
     */
    public function getPosts()
    {
        return $this->postRepo->getAll(["category_id" => $this->id]);
    }

    public function update(array $newData): bool
    {
        $success = $this->categoryRepo->update($this, $newData);
        if ($success) {
            foreach ($newData as $field => $value) {
                $this->{$field} = $value;
            }
        }
        return $success;
    }

    public function delete(): bool
    {
        if ($this->categoryRepo->delete($this)) {
            parent::delete();
            return true;
        }
        return false;
    }

    public function getLink(string $routeName = "category")
    {
        return parent::getLink($routeName);
    }
}
