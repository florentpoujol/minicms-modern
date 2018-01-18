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


    public static function connect(\PDO $connection = null)
    {
        if ($connection === null) {
            $host = Config::get("db_host");
            $name = Config::get("db_name");
            $user = Config::get("db_user");
            $password = Config::get("db_password");

            try {
                self::$db = new PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $password, self::$pdoOptions);
            } catch (\Exception $e) {
                echo "Error connecting to the database: probably wrong host, username or password.<br>";
                echo $e->getMessage();
                exit;
            }
        } else {
            self::$db = $connection;
        }
    }

    /**
     * @return bool|PDO
     */
    public static function testConnection(string $host, string $name, string $user, string $password)
    {
        // todo : validate host, name and user format
        try {
            return new PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $password, self::$pdoOptions);
        } catch (\Exception $e) {
            Messages::addError("Error connecting to the database: probably wrong host, username or password.");
            Messages::addError($e->getMessage());
        }
        return false;
    }


    /**
     * Attempt to create the database, its structure and to populate it during the site's install process
     * @param array $dbConnectionInfo Array containing the connection information to the database.
     * @param array $userInfo Array containing the information about the first user.
     * @return bool
     */
    public static function install(array $dbConnectionInfo, array $userInfo)
    {
        // things to do in order :
        // test connection to db
        // create DB if not exist
        // read sql file
        // create table if not exists
        // populate config and user

        $db = self::testConnection(
            $dbConnectionInfo["db_host"],
            $dbConnectionInfo["db_name"],
            $dbConnectionInfo["db_user"],
            $dbConnectionInfo["db_password"]
        );

        if ($db !== false) {
            $success = $db->exec("CREATE DATABASE IF NOT EXISTS `" . $dbConnectionInfo["db_name"] . "`");
            if ($success) {
                $db->exec("use `" . $dbConnectionInfo["db_name"] . "`");

                $sql = file_get_contents(self::$dbStructureFile);
                $success = $db->exec($sql); // false on error, on success in this case

                if ($success !== false) {
                    $user = User::create($userInfo);

                    $defaultMenu = [
                        [
                            "type" => "external",
                            "name" => "Login/Admin",
                            "target" => "?r=admin",
                            "children" => []
                        ],
                        [
                            "type" => "external",
                            "name" => "Logout",
                            "target" => "?r=logout",
                            "children" => []
                        ]
                    ];
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
     * @param mixed $value
     */
    public static function valueExistsInDB($value, string $field, string $table): bool
    {
        $query = self::$db->prepare("SELECT id FROM $table WHERE $field = ?");
        $success = $query->execute([$value]);

        if ($success) {
            return $query->fetch() !== false;
        }
        return false;
    }
}
