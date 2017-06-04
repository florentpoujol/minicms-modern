<?php

namespace App;

class Database
{
    /**
     * @var \PDO
     * The PDO instance
     */
    protected static $db;

    public static function connect()
    {
        $host = Config::get("db_host");
        $name = Config::get("db_name");
        $user = Config::get("db_user");
        $password = Config::get("db_password");

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            self::$db = new \PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $password, $options);
        }
        catch (\Exception $e) {
            echo "error connecting to the database <br>";
            echo $e->getMessage();
            exit();
        }
    }

    /**
     * @param string $tableName The table name
     * @param array $params One or several WHERE clause from which to find the user. The keys must match the database fields names.
     * @param string $condition Should be AND or OR
     * @return \App\Entities\Entity|bool Entity populated from DB data or false on error
     */
    public static function getFromTable($tableName, $className, $params, $condition = "AND")
    {
        $strQuery = "SELECT * FROM $tableName WHERE ";

        foreach ($params as $name => $value) {
            $strQuery .= "$name=:$name $condition ";
        }

        $strQuery = rtrim($strQuery," $condition ");

        $query = self::$db->prepare($strQuery);
        $query->setFetchMode(\PDO::FETCH_CLASS, "App\Entities\\$className");
        $success = $query->execute($params);

        if ($success === true) {
            return $query->fetch();
        }

        return false;
    }
}
