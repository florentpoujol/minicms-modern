<?php

namespace App;

use \PDO;

class Database
{
    /**
     * @var PDO
     * The PDO instance
     */
    protected static $db;

    public static function connect($connexion = null)
    {
        if ($connexion === null) {
            $host = Config::get("db_host");
            $name = Config::get("db_name");
            $user = Config::get("db_user");
            $password = Config::get("db_password");

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$db = new PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $password, $options);
            } catch (\Exception $e) {
                echo "error connecting to the database <br>";
                echo $e->getMessage();
                exit();
            }
        } else {
            self::$db = $connexion;
        }
    }
}
