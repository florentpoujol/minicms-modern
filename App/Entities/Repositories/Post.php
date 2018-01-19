<?php

namespace App\Entities\Repositories;

use App\Config;
use App\Database;
use App\Entities\Category as CategoryEntity;
use App\Entities\Post as PostEntity;
use App\Entities\Comment as CommentEntity;
use App\Entities\User as UserEntity;

class Post extends Entity
{
    /**
     * @var Category
     */
    public $categoryRepo;
    /**
     * @var Comment
     */
    public $commentRepo;

    /**
     * @var User
     */
    public $userRepo;

    public function __construct(Database $database, Config $config)
    {
        parent::__construct($database, $config);
        // $this->categoryRepo = $categoryRepo;
        // $this->commentRepo = $CommentRepo;
        // $this->userRepo = $userRepo;
    }

    /**
     * @return PostEntity|false
     */
    public function get($whereConditions, bool $useWhereOrOperator = false)
    {
        return parent::get($whereConditions, $useWhereOrOperator);
    }

    /**
     * @return PostEntity[]|false
     */
    public function getAll(array $params = [])
    {
        return parent::getAll($params);
    }

    /**
     * @return User|bool
     */
    public function getUser(PostEntity $post)
    {
        return $this->userRepo->get($post->id);
    }

    /**
     * @return CategoryEntity|bool
     */
    public function getCategory(PostEntity $entity)
    {
        return $this->categoryRepo->get($entity->id);
    }

    /**
     * @return CommentEntity[]|bool
     */
    public function getComments(PostEntity $post)
    {
        return $this->commentRepo->getAll(["post_id" => $post->id]);
    }

    /**
     * @return PostEntity|false
     */
    public function create(array $data)
    {
        return parent::create($data);
    }

    public function delete($post): bool
    {
        if (parent::delete($post)) {
            /*$comments = $this->getComments($post);
            foreach ($comments as $comment) {
                $this->commentRepo->delete($comment);
            }*/
            return true;
        }
        return false;
    }


}
