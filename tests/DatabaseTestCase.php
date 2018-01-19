<?php

namespace Tests;

use App\Database;
use App\Entities\Entity;
use App\Entities\Repositories\Category;
use App\Entities\Repositories\Post;
use PDO;
use PHPUnit\DbUnit\DataSet\YamlDataSet;
use PHPUnit\DbUnit\TestCaseTrait;

abstract class DatabaseTestCase extends BaseTestCase
{
    use TestCaseTrait;

    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var Category
     */
    protected $categoryRepo;

    /**
     * @var Post
     */
    protected $postRepo;


    final public function getConnection()
    {
        if (self::$pdo === null) {
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            self::$pdo = new PDO($GLOBALS["DB_DSN"], $GLOBALS["DB_USER"], $GLOBALS["DB_PASSWORD"], $options);
        }

        $this->database = $this->container->get(Database::class);
        $this->database->pdo = self::$pdo;

        $this->categoryRepo = $this->container->get(Category::class);
        $this->postRepo = $this->container->get(Post::class);

        $this->categoryRepo->postRepo = $this->postRepo;
        $this->postRepo->postRepo = $this->categoryRepo;

        return $this->createDefaultDBConnection(self::$pdo, $GLOBALS["DB_NAME"]);
    }

    public function getDataSet()
    {
        return new YamlDataSet(__dir__ . "/testsDataSet.yml");
    }
}
