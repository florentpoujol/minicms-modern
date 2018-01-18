<?php

namespace App;

use App\Entities\Menu;
use App\Entities\User;
use \PDO;
use StdCmp\QueryBuilder\QueryBuilder;

class Database
{
    public $dbStructureFile = __dir__ . "/../database_structure.sql";

    /**
     * @var PDO
     * The PDO instance
     */
    public $pdo;

    private $pdoOptions = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    /**
     * @var Config
     */
    public $config;

    /**
     * @var Session
     */
    public $session;

    public function __construct(Config $config, Session $session)
    {
        $this->config = $config;
        $this->session = $session;
    }

    public function connect(array $connectionInfo = null)
    {
        if ($connectionInfo === null) {
            $connectionInfo = [
                "db_host" => $this->config->get("db_host"),
                "db_name" => $this->config->get("db_name"),
                "db_user" => $this->config->get("db_user"),
                "db_password" => $this->config->get("db_password"),
            ];
        }

        try {
            $dsn = "mysql:host=$connectionInfo[db_host];dbname=$connectionInfo[db_name];charset=utf8";
            $this->pdo = new PDO($dsn, $connectionInfo["db_user"], $connectionInfo["db_password"], $this->pdoOptions);
        } catch (\Exception $e) {
            echo "Error connecting to the database: probably wrong host, username or password.<br>";
            echo $e->getMessage();
            exit;
        }
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this->pdo);
    }

    /**
     * @return bool|PDO
     */
    public function testConnection(array $connectionInfo)
    {
        // todo : validate host, name and user format ?
        try {
            $dsn = "mysql:host=$connectionInfo[db_host];dbname=$connectionInfo[db_name];charset=utf8";
            return new PDO($dsn, $connectionInfo["db_user"], $connectionInfo["db_password"], $this->pdoOptions);
        } catch (\Exception $e) {
            $this->session->addError("Error connecting to the database: probably wrong host, username or password.");
            $this->session->addError($e->getMessage());
        }
        return false;
    }

    /**
     * Attempt to create the database, its structure and to populate it during the site's install process
     * @param array $dbConnectionInfo Array containing the connection information to the database.
     * @param array $userInfo Array containing the information about the first user.
     * @return bool
     */
    public function install(array $dbConnectionInfo, array $userInfo)
    {
        $db = $this->testConnection($dbConnectionInfo);
        // register $db to the container
        // register the $querybuilder
        if ($db !== false) {
            $success = $db->exec("CREATE DATABASE IF NOT EXISTS `" . $dbConnectionInfo["db_name"] . "`");
            if ($success) {
                $db->exec("use `" . $dbConnectionInfo["db_name"] . "`");

                $sql = file_get_contents($this->dbStructureFile);
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
                        $this->session->addError("install.populatingdb");
                    }
                } else {
                    $this->session->addError("install.createtables");
                }
            } else {
                $this->session->addError("install.createdb");
            }
        }
        return false;
    }

    /**
     * @param mixed $value
     */
    public function valueExistsInDB($value, string $field, string $table): bool
    {
        $query = $this->pdo->prepare("SELECT id FROM $table WHERE $field = ?");
        $success = $query->execute([$value]);

        if ($success) {
            return $query->fetch() !== false;
        }
        return false;
    }
}
