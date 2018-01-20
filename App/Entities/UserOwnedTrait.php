<?php

namespace App\Entities;

use App\Entities\Repositories\User as UserRepo;

trait UserOwnedTrait
{
    /**
     * @var UserRepo
     */
    public $userRepo;

    /**
     * @return User|false
     */
    public function getUser()
    {
        return $this->userRepo->get(["id" => $this->user_id]);
    }
}
