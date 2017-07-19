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
     * @param array $newUser
     * @return User|bool
     */
    public static function create($newUser)
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
     * Get all the posts created by that user
     * @return Post[]|bool
     */
    public function getPosts()
    {
        return Post::getAll(["user_id" => $this->id]);
    }

    /**
     * Get all the comments created by that user
     * @return Comment[]|bool
     */
    public function getComments()
    {
        return Comment::getAll(["user_id" => $this->id]);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function update($data)
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

    /**
     * @param string $token
     * @return bool
     */
    public function updatePasswordToken($token)
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

    /**
     * @param string $password
     * @return bool
     */
    public function updatePassword($password)
    {
        return $this->update([
            "password_token" => "",
            "password_change_time" => 0,
            "password_hash" => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    /**
     * @param string $token
     * @return bool
     */
    public function updateEmailToken($token)
    {
        return $this->update(["email_token" => $token]);
    }

    /**
     * @param bool $block
     * @return bool
     */
    public function block($block = true)
    {
        return $this->update(["is_blocked" => ($block ? 1 : 0)]);
    }

    /**
     * @param int $adminId The id of the admin user that delete this user
     * @return bool
     */
    public function deleteByAdmin($adminId)
    {
        $rows = $this->getComments();
        foreach ($rows as $row) {
            $row->delete();
        }

        $rows = $this->getPosts();
        foreach ($rows as $row) {
            $row->update(["user_id" => $adminId]);
        }

        return parent::delete();
    }

    public function isAdmin()
    {
        return ($this->role === "admin");
    }

    public function isWriter()
    {
        return ($this->role === "writer");
    }

    public function isCommenter()
    {
        return ($this->role === "commenter");
    }

    public function isBlocked()
    {
        return ($this->is_blocked === 1);
    }
}
