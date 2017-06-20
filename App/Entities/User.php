<?php

namespace App\Entities;

use App\Security;

/**
 * Class User
 * Instances represents the logged in user.
 * @package App\Entities
 */
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
    public $creation_datetime;

    public function __construct()
    {
        parent::__construct();
        $this->table = "users";
    }

    /**
     * @param array $params
     * @param string $condition
     * @return User|bool
     */
    public static function get($params, $condition = "AND")
    {
        return parent::_get($params, $condition, "users", "User");
    }

    /**
     * @param array $params
     * @return User[]|bool
     */
    public static function getAll($params = [])
    {
        return parent::_getAll($params, "users", "User");
    }

    public static function countAll()
    {
        return parent::_countAll("users");
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

    /**
     * @param array $newUser
     * @return User|bool
     */
    public static function create($newUser)
    {
        unset($newUser["id"]);

        $query = self::$db->prepare("SELECT id FROM users");
        $query->execute();
        if ($query->fetch() === false) {
            // the first user gets to be admin
            $newUser["role"] = "admin";
        }

        if (! isset($newUser["role"])) {
            $newUser["role"] = "commenter";
        }

        $newUser["email_token"] = Security::getUniqueToken();

        $newUser["password_hash"] = password_hash($newUser["password"], PASSWORD_DEFAULT);
        $newUser["password_token"] = "";

        unset($newUser["password"]);
        unset($newUser["password_confirmation"]);

        $newUser["creation_datetime"] = date("Y-m-d H:i:s");

        $query = self::$db->prepare("INSERT INTO users(name, email, email_token, password_hash, password_token, role, creation_datetime)
            VALUES(:name, :email, :email_token, :password_hash, :password_token, :role, :creation_datetime)");
        $success = $query->execute($newUser);

        if ($success) {
            return self::get(["id" => self::$db->lastInsertId()]);
        }
        return false;
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

    /**
     * @param int $adminId The id of the admin user that delete this user
     * @return bool
     */
    public function delete($adminId)
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

        return parent::_delete();
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
}
