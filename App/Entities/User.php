<?php

namespace App\Entities;

class User extends Entity
{
    public $name;
    public $email;
    public $email_token;
    public $password_hash;
    public $password_token;
    public $password_change_time;
    public $role;
    public $is_blocked;

    /**
     * @return User|bool
     */
    public static function create(array $newUser)
    {
        $query = self::$db->prepare("SELECT id FROM users");
        $query->execute();
        if ($query->fetch() === false) {
            // the first user gets to be admin
            $newUser["role"] = "admin";
        }

        if (! isset($newUser["role"])) {
            $newUser["role"] = "commenter";
        }

        $newUser["email_token"] = \App\Security::getUniqueToken();

        $newUser["password_hash"] = password_hash($newUser["password"], PASSWORD_DEFAULT);
        $newUser["password_token"] = "";

        unset($newUser["password"]);
        unset($newUser["password_confirmation"]);

        $newUser["is_blocked"] = 0;

        return parent::create($newUser);
    }

    /**
     * @return Post[]|bool
     */
    public function getPosts()
    {
        return Post::getAll(["user_id" => $this->id]);
    }

    /**
     * @return Comment[]|bool
     */
    public function getComments()
    {
        return Comment::getAll(["user_id" => $this->id]);
    }

    /**
     * @return Media[]|bool
     */
    public function getMedias()
    {
        return Media::getAll(["user_id" => $this->id]);
    }

    public function update(array $data): bool
    {
        if (isset($data["password"])) {
            $password = $data["password"];
            if ($password !== "") {
                $this->updatePassword($password);
            }
            unset($data["password"]);
            unset($data["password_confirmation"]);
        }
        return parent::update($data);
    }

    public function updatePasswordToken(string $token): bool
    {
        $time = 0;
        if ($token !== "") {
            $time = time();
        }

        return $this->update([
            "password_token" => $token,
            "password_change_time" => $time
        ]);
    }

    public function updatePassword(string $password): bool
    {
        return $this->update([
            "password_token" => "",
            "password_change_time" => 0,
            "password_hash" => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public function updateEmailToken(string $token): bool
    {
        return $this->update(["email_token" => $token]);
    }

    public function block(bool $block = true): bool
    {
        return $this->update(["is_blocked" => ($block ? 1 : 0)]);
    }

    public function deleteByAdmin(int $adminUserId): bool
    {
        $rows = $this->getComments();
        foreach ($rows as $row) {
            $row->delete();
        }

        $rows = array_merge($this->getPosts(), $this->getMedias());
        foreach ($rows as $row) {
            $row->update(["user_id" => $adminUserId]);
        }

        return parent::delete();
    }

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
