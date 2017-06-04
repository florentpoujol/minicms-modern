<?php

namespace App\Entities;

/**
 * Class User
 * Instances represents the logged in user.
 * @package App\Entities
 */
class User extends Entity
{
    public $id;
    public $name;
    public $email;
    public $email_token;
    public $password_hash;
    public $password_token;
    public $password_change_time;
    public $role;
    public $creation_datetime;

    /**
     * @param array $params
     * @param string $condition
     * @return bool|User
     */
    public static function get($params, $condition = "AND")
    {
        return parent::getFromTable("users", "User", $params, $condition);
    }

    /**
     * @param array $newUser
     * @return User|bool
     */
    public static function insert($newUser)
    {
        if (isset($newUser["role"]) === false) {
            $newUser["role"] = "commenter";
        }

        $query = self::$db->prepare("SELECT id FROM users");
        $query->execute();
        $user = $query->fetch();
        if ($user === false) {
            // the first user gets to be admin
            $newUser["role"] = "admin";
        }

        $newUser["email_token"] = md5(microtime(true)+mt_rand());
        $newUser["password_hash"] = password_hash($newUser["password"], PASSWORD_DEFAULT);
        // $newUser["password_token"] = "";
        unset($newUser["password"]);
        $newUser["creation_datetime"] = date("Y-m-d H:i:s");

        $query = self::$db->prepare("INSERT INTO users(name, email, email_token, password_hash, role, creation_datetime)
            VALUES(:name, :email, :email_token, :password_hash, :role, :creation_date)");
        $success = $query->execute($newUser);

        if ($success === true) {
            return self::get(["id" => self::$db->lastInsertId()]);
        }

        return false;
    }

    public function __construct()
    {

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

    // return true is user has one of the roles
    public function hasRoles($role1, $role2 = null)
    {
        $hasRole = ($this->role === $role1);

        if (! $hasRole && isset($role2)) {
            $hasRole = ($this->role === $role2);
        }

        return $hasRole;
    }

    public function updatePasswordToken($token)
    {
        $query = self::$db->prepare("UPDATE users SET password_token=:token, password_change_time=:time WHERE id=:id");
        $params = [
            "id" => $this->id,
            "token" => $token,
            "time" => time()
        ];

        $success = $query->execute($params);
        if ($success) {
            $this->password_token = $token;
        }

        return $success;
    }

    public function updatePassword($password)
    {
        $query = self::$db->prepare("UPDATE users SET password_token='', password_change_time=0, password_hash=:hash WHERE id=:id");
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $params = [
            "id" => $this->id,
            "hash" => $hash
        ];

        $success = $query->execute($params);
        if ($success) {
            $this->password_hash = $hash;
        }

        return $success;
    }

    public function updateEmailToken($token)
    {
        $query = self::$db->prepare("UPDATE users SET email_token=:token WHERE id=:id");
        $params = [
            "id" => $this->id,
            "token" => $token
        ];

        $success = $query->execute($params);
        if ($success) {
            $this->email_token = $token;
        }

        return $success;
    }
}
