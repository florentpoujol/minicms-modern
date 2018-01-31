<?php

namespace App\Entities\Repositories;

use App\Config;
use App\Database;
use App\Entities\User as UserEntity;
use App\Helpers;
use App\Session;

class User extends Entity
{
    /**
     * @var Comment
     */
    protected $commentRepo;

    /**
     * @var Page
     */
    protected $pageRepo;

    /**
     * @var Post
     */
    protected $postRepo;

    /**
     * @var Media
     */
    protected $mediaRepo;

    public function __construct(
        Database $database, Config $config, Session $session,
        Comment $commentRepo, Page $pageRepo, Post $postRepo, Media $mediaRepo)
    {
        parent::__construct($database, $config, $session);
        $this->commentRepo = $commentRepo;
        $this->pageRepo = $pageRepo;
        $this->postRepo = $postRepo;
        $this->mediaRepo = $mediaRepo;
    }

    /**
     * @return UserEntity|false
     */
    public function get($whereConditions, bool $useWhereOrOperator = false)
    {
        return parent::get($whereConditions, $useWhereOrOperator);
    }

    /**
     * @return UserEntity[]|false
     */
    public function getAll(array $params = [])
    {
        return parent::getAll($params);
    }

    /**
     * @return UserEntity|bool
     */
    public function create(array $newUser)
    {
        if ($this->countAll() === 0) {
            // the first user gets to be admin
            $newUser["role"] = "admin";
        }

        if (!isset($newUser["role"])) {
            $newUser["role"] = "commenter";
        }

        if (!isset($newUser["email_token"])) {
            $newUser["email_token"] = (new Helpers())->getUniqueToken();
        }

        $newUser["password_hash"] = password_hash($newUser["password"], PASSWORD_DEFAULT);
        $newUser["password_token"] = "";

        unset($newUser["password"]);
        unset($newUser["password_confirmation"]);

        $newUser["is_blocked"] = 0;

        return parent::create($newUser);
    }

    /**
     * @param UserEntity $user
     */
    public function update($user, array $data): bool
    {
        if (isset($data["password"])) {
            $password = $data["password"];
            if ($password !== "") {
                $user->updatePassword($password);
            }
            unset($data["password"]);
            unset($data["password_confirmation"]);
        }
        return parent::update($user, $data);
    }

    /**
     * @param UserEntity $user
     */
    public function deleteByAdmin($user, int $adminUserId): bool
    {
        if (parent::delete($user)) {
            $whereConditions = ["user_id" => $user->id];
            $this->commentRepo->deleteMany($whereConditions);

            $newData = ["user_id" => $adminUserId];
            $this->postRepo->updateMany($newData, $whereConditions);
            $this->pageRepo->updateMany($newData, $whereConditions);
            $this->mediaRepo->updateMany($newData, $whereConditions);
            return true;
        }
        return false;
    }
}
