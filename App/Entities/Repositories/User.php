<?php

namespace App\Entities\Repositories;

use App\Entities\Comment as CommentEntity;
use App\Entities\Page as PageEntity;
use App\Entities\Post as PostEntity;
use App\Entities\Media as MediaEntity;
use App\Entities\User as UserEntity;
use App\Helpers;

class User extends Entity
{
    /**
     * @var Comment
     */
    public $commentRepo;

    /**
     * @var Page
     */
    public $pageRepo;

    /**
     * @var Post
     */
    public $postRepo;

    /**
     * @var Media
     */
    public $mediaRepo;

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
     * @return CommentEntity[]|bool
     */
    public function getComments(UserEntity $user)
    {
        return $this->commentRepo->getAll(["user_id" => $user->id]);
    }

    /**
     * @return PostEntity[]|bool
     */
    public function getPosts(UserEntity $user)
    {
        return $this->postRepo->getAll(["user_id" => $user->id]);
    }

    /**
     * @return PageEntity[]|bool
     */
    public function getPages(UserEntity $user)
    {
        return $this->pageRepo->getAll(["user_id" => $user->id]);
    }

    /**
     * @return MediaEntity[]|bool
     */
    public function getMedias(UserEntity $user)
    {
        return $this->mediaRepo->getAll(["user_id" => $user->id]);
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

        $newUser["email_token"] = (new Helpers())->getUniqueToken();

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
                $this->updatePassword($user, $password);
            }
            unset($data["password"]);
            unset($data["password_confirmation"]);
        }
        return parent::update($user, $data);
    }

    /**
     * @param UserEntity $user
     */
    public function updatePasswordToken($user, string $token): bool
    {
        return $this->update($user, [
            "password_token" => $token,
            "password_change_time" => $token !== "" ? time() : 0,
        ]);
    }

    /**
     * @param UserEntity $user
     */
    public function updatePassword($user, string $password): bool
    {
        return $this->update($user, [
            "password_token" => "",
            "password_change_time" => 0,
            "password_hash" => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    /**
     * @param UserEntity $user
     */
    public function updateEmailToken($user, string $token): bool
    {
        return $this->update($user, ["email_token" => $token]);
    }

    /**
     * @param UserEntity $user
     */
    public function block($user, bool $block = true): bool
    {
        return $this->update($user, ["is_blocked" => (int)$block]);
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
