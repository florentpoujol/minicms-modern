<?php

namespace App\Entities;

class User extends Entity
{
    public $name = "";
    public $email = "";
    public $email_token = "";
    public $password_hash = "";
    public $password_token = "";
    public $password_change_time = -1;
    public $role = "";
    public $is_blocked = -1;

    public function isAdmin(): bool
    {
        return $this->role === "admin";
    }

    public function isWriter(): bool
    {
        return $this->role === "writer";
    }

    public function isCommenter(): bool
    {
        return $this->role === "commenter";
    }

    public function isBlocked(): bool
    {
        return $this->is_blocked === 1;
    }
}
