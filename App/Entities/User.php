<?php

namespace App\Entities;

class User extends Entity
{
    // fields from DB
    public $name;
    public $email;
    public $email_token;
    public $password_hash;
    public $password_token;
    public $password_change_time;
    public $role;
    public $is_blocked;

    /**
     * @return User|false
     */
    public static function get($params, $condition = "AND")
    {
        // note: redeclaring a method like that seems necessary due to a probable bug
        // in PHPStorm that does not properly handle a return type  $this|bool on the parent method
        return parent::get($params, $condition);
    }

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
     * Get all the page created by that user
     * @return Page[]|bool
     */
    public function getPages()
    {
        return Page::getAll(["user_id" => $this->id]);
    }

    /**
     * Get all the comments created by that user
     * @return Comment[]|bool
     */
    public function getComments()
    {
        return Comment::getAll(["user_id" => $this->id]);
    }

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

    public function updatePassword($password)
    {
        return $this->update([
            "password_token" => "",
            "password_change_time" => 0,
            "password_hash" => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public function updateEmailToken($token)
    {
        return $this->update(["email_token" => $token]);
    }

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

        $rows = $this->getPages();
        foreach ($rows as $row) {
            $row->update(["user_id" => $adminId]);
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
