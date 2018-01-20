<?php

namespace App\Entities\Repositories;

use App\Config;
use App\Database;
use App\Entities\Category as CategoryEntity;
use App\Session;

class Category extends Entity
{
    /**
     * @var Post
     */
    protected $postRepo;

    public function __construct(Database $database, Config $config, Session $session, Post $postRepo)
    {
        parent::__construct($database, $config, $session);
        $this->tableName = "categories";
        $this->postRepo = $postRepo;
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
