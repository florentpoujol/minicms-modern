<?php

namespace App\Models;

class Model
{
    // holds the PDO instance
    protected static $db;

    // creates the connection to the database
    public static function connect()
    {
        $host = \App\Config::get("db_host");
        $name = \App\Config::get("db_name");
        $user = \App\Config::get("db_user");
        $password = \App\Config::get("db_password");

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            self::$db = new \PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $password, $options);
        }
        catch (Exception $e) {
            echo "error connecting to the database <br>";
            echo $e->getMessage();
            exit();
        }
    }
}
