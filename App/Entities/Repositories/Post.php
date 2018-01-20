<?php

namespace App\Entities\Repositories;

use App\Config;
use App\Database;
use App\Entities\Post as PostEntity;
use App\Session;

class Post extends Entity
{
    /**
     * @var Comment
     */
    protected $commentRepo;

    public function __construct(Database $database, Config $config, Session $session, Comment $commentRepo)
    {
        parent::__construct($database, $config, $session);
        $this->commentRepo = $commentRepo;
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
     * @return PostEntity|false
     */
    public function create(array $data)
    {
        return parent::create($data);
    }

    /**
     * @param PostEntity $post
     */
    public function delete($post): bool
    {
        if (parent::delete($post)) {
            $this->commentRepo->deleteMany(["post_id" => $post->id]);
            return true;
        }
        return false;
    }
}
