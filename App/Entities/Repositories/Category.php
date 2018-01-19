<?php

namespace App\Entities\Repositories;

use App\Config;
use App\Database;
use App\Entities\Entity as BaseEntity;
use App\Entities\Category as CategoryEntity;
use App\Entities\Post as PostEntity;

class Category extends Entity
{
    /**
     * @var Post
     */
    public $postRepo;

    public function __construct(Database $database, Config $config)
    {
        parent::__construct($database, $config);
        $this->tableName = "categories";
        // $this->postRepo = $postRepo;
    }

    /**
     * @return CategoryEntity|false
     */
    public function get($whereConditions, bool $useWhereOrOperator = false)
    {
        return parent::get($whereConditions, $useWhereOrOperator);
    }

    /**
     * @return CategoryEntity[]|false
     */
    public function getAll(array $params = [])
    {
        return parent::getAll($params);
    }

    /**
     * @return CategoryEntity|false
     */
    public function create(array $data)
    {
        return parent::create($data);
    }

    public function delete($category): bool
    {
        if (parent::delete($category)) {
            $posts = $this->getPosts($category);
            foreach ($posts as $post) {
                $this->postRepo->delete($post);
            }
            return true;
        }
        return false;
    }

    /**
     * @return PostEntity[]|bool
     */
    public function getPosts(BaseEntity $entity, array $whereConditions = [])
    {
        $whereConditions = array_merge(["category_id" => $entity->id], $whereConditions);
        return $this->postRepo->getAll($whereConditions);
    }
}
