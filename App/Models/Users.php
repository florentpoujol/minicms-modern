<?php

namespace App\Models;

class Users extends Model
{

    public static function get($params, $condition = "AND")
    {
        $strQuery = "SELECT * FROM users WHERE ";
        foreach ($params as $name => $value) {
            $strQuery .= "$name=:$name $condition ";
        }

        $strQuery = rtrim($strQuery," $condition ");

        $query = self::$db->prepare($strQuery);
        $success = $query->execute($params);

        if ($success === true) {
            return $query->fetch();
        }

        return false;
    }

    // --------------------------------------------------

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
        $newUser["creation_date"] = date("Y-m-d");

        $query = self::$db->prepare("INSERT INTO users(name, email, email_token, password_hash, role, creation_date)
            VALUES(:name, :email, :email_token, :password_hash, :role, :creation_date)");
        $success = $query->execute($newUser);

        if ($success === true) {
            return self::$db->lastInsertId();
        }

        return false;
    }

    // --------------------------------------------------

    public static function updatePasswordToken($userId, $token)
    {
        $query = self::$db->prepare("UPDATE users SET password_token=:token, password_change_time=:time WHERE id=:id");
        $params = [
            "id" => $userId,
            "token" => $token,
            "time" => time()
        ];
        return $query->execute($params);
    }

    public static function updatePassword($userId, $password)
    {
        $query = self::$db->prepare("UPDATE users SET password_token='', password_change_time=0, password_hash=:hash WHERE id=:id");
        $params = [
            "id" => $userId,
            "hash" => password_hash($password, PASSWORD_DEFAULT)
        ];
        return $query->execute($params);
    }

    public static function updateEmailToken($userId, $token = "")
    {
        $query = self::$db->prepare("UPDATE users SET email_token=:token WHERE id=:id");
        $params = [
            "id" => $userId,
            "token" => $token
        ];
        return $query->execute($params);
    }
}
