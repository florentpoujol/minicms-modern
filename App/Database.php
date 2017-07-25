<?php

namespace App;

use App\Entities\Menu;
use App\Entities\User;
use \PDO;

class Database
{
    public static $dbStructureFile = __dir__ . "/../database_structure.sql";

    /**
     * @var PDO
     * The PDO instance
     */
    protected static $db;

    private static $pdoOptions = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    public static function connect($connexion = null)
    {
        if ($connexion === null) {
            $host = Config::get("db_host");
            $name = Config::get("db_name");
            $user = Config::get("db_user");
            $password = Config::get("db_password");

            try {
                self::$db = new PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $password, self::$pdoOptions);
            } catch (\Exception $e) {
                echo "error connecting to the database <br>";
                echo $e->getMessage();
                exit();
            }
        } else {
            self::$db = $connexion;
        }
    }

    /**
     * @param string $host
     * @param string $name
     * @param string $user
     * @param string $password
     * @return bool|PDO
     */
    public static function testConnection($host, $name, $user, $password)
    {
        // todo : validate host, name and user format
        $conn = false;
        try {
            $conn = new PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $password, self::$pdoOptions);
        } catch (\Exception $e) {
            Messages::addError("Error connecting to the database: probably wrong host, username or password.");
            Messages::addError($e->getMessage());
            $conn = false;
        }
        return $conn;
    }

    /**
     * @param PDO $db
     * @return bool
     */
    public static function install($configPost, $userPost)
    {
        // things to do in order :
        // test connection to db
        // create DB if not exist
        // read sql file
        // create table if not exists
        // populate config and user

        $db = self::testConnection(
            $configPost["db_host"],
            $configPost["db_name"],
            $configPost["db_user"],
            $configPost["db_password"]
        );

        if ($db !== false) {
            $success = $db->query("CREATE DATABASE IF NOT EXISTS `" . $configPost["db_name"] . "`");
            if ($success) {
                $db->query("use `" . $configPost["db_name"] . "`");

                $sql = file_get_contents(self::$dbStructureFile);
                $query = $db->prepare($sql);
                $success = $query->execute();

                if ($success) {
                    $user = User::create($userPost);

                    $defaultMenu = [[
                        "type" => "external",
                        "name" => "Login",
                        "target" => "?r=login",
                        "children" => []
                    ]];
                    $menu = [
                        "slug" => "defaultmenu",
                        "in_use" => 1,
                        "structure" => json_encode($defaultMenu, JSON_PRETTY_PRINT)
                    ];
                    $menu = Menu::create($menu);

                    if (is_object($user) && is_object($menu)) {
                        return true;
                    } else {
                        Messages::addError("install.populatingdb");
                    }
                } else {
                    Messages::addError("install.createtables");
                }
            } else {
                Messages::addError("install.createdb");
            }
        }
        return false;
    }

    /**
     * @param string $value
     * @param string $field
     * @param string $table
     * @return bool
     */
    public static function valueExistsInDB($value, $field, $table)
    {
        $query = self::$db->prepare("SELECT id FROM $table WHERE $field = ?");
        $success = $query->execute([$value]);

        if ($success) {
            return ($query->fetch() !== false);
        }
        return false;
    }
}
