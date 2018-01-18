<?php

namespace Tests;

use App\Config;
use App\Database;
use App\Session;
use PDO;
use PHPUnit\DbUnit\TestCaseTrait;

abstract class DatabaseTestCase extends BaseTestCase
{
    use TestCaseTrait;

    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    /**
     * @var Database
     */
    static private $db;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];

                self::$pdo = new PDO($GLOBALS["DB_DSN"], $GLOBALS["DB_USER"], $GLOBALS["DB_PASSWORD"], $options);

                self::$db = new Database($this->config, $this->session);
                self::$db->pdo = self::$pdo;
            }

            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS["DB_NAME"]);
        }

        return $this->conn;
    }

    public function getDataSet()
    {
        /*$thing =
            new \PHPUnit\DbUnit\DataSet\YamlDataSet(
                __dir__ . "/mainDataSet.yml"
            );
        return $thing;*/
        return null;
    }
}
