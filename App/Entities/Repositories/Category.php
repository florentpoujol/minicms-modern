<?php

namespace App\Entities\Repositories;

use App\Config;
use App\Database;
use App\Entities\Entity as BaseEntity;
use App\Entities\Category as CategoryEntity;
use App\Entities\Post as PostEntity;
use App\Session;

class Category extends Entity
{
    /**
     * @var Post
     */
    public $postRepo;

    public function __construct(Database $database, Config $config, Session $session)
    {
        parent::__construct($database, $config, $session);
        $this->tableName = "categories";
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
     * @return PostEntity[]|bool
     */
    public function getPosts(BaseEntity $entity, array $whereConditions = [])
    {
        $whereConditions = array_merge(["category_id" => $entity->id], $whereConditions);
        return $this->postRepo->getAll($whereConditions);
    }

    /**
     * @return CategoryEntity|false
     */
    public function create(array $data)
    {
        return parent::create($data);
    }

    /**
     * @param CategoryEntity $category
     */
    public function delete($category): bool
    {
        if (parent::delete($category)) {
            $this->postRepo->deleteMany(["category_id" => $category->id]);
            return true;
        }
        return false;
    }
}
