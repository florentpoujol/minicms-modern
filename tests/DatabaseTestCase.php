<?php

namespace Tests;

use App\Database;
use App\Entities\Repositories\Category;
use App\Entities\Repositories\Comment;
use App\Entities\Repositories\Media;
use App\Entities\Repositories\Menu;
use App\Entities\Repositories\Page;
use App\Entities\Repositories\Post;
use App\Entities\Repositories\User;
use PHPUnit\DbUnit\DataSet\YamlDataSet;
use PHPUnit\DbUnit\TestCaseTrait;

abstract class DatabaseTestCase extends BaseTestCase
{
    use TestCaseTrait;

    // only instantiate pdo once for test clean-up/fixture load
    protected static $pdo = null;

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
    /**
     * @var Page
     */
    protected $pageRepo;
    /**
     * @var Comment
     */
    protected $commentRepo;
    /**
     * @var User
     */
    protected $userRepo;
    /**
     * @var Media
     */
    protected $mediaRepo;
    /**
     * @var Menu
     */
    protected $menuRepo;

    public static function createDB()
    {
        $config = json_decode(file_get_contents(__dir__ . "/testsConfig.json"), true);

        $pdo = new \PDO(
            "mysql:host=$config[db_host];charset=utf8",
            $config["db_user"],
            $config["db_password"],
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );

        $pdo->exec("DROP DATABASE IF EXISTS $config[db_name]");
        $pdo->exec("CREATE DATABASE $config[db_name]");
        $pdo->exec("use $config[db_name]");

        $structure = file_get_contents(__dir__ . "/../database_structure.sql");
        $pdo->exec($structure);
    }

    public function getConnection()
    {
        if (self::$pdo === null) {
            $options = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $dsn = "mysql:host=" . $this->config->get("db_host") . ";dbname=" . $this->config->get("db_name") . ";charset=utf8";

            self::$pdo = new \PDO(
                $dsn,
                $this->config->get("db_user"),
                $this->config->get("db_password"),
                $options
            );
        }

        $this->database = $this->container->get(Database::class);
        $this->database->pdo = self::$pdo;

        $this->categoryRepo = $this->container->get(Category::class);
        $this->commentRepo = $this->container->get(Comment::class);
        $this->pageRepo = $this->container->get(Page::class);
        $this->postRepo = $this->container->get(Post::class);
        $this->userRepo = $this->container->get(User::class);
        $this->mediaRepo = $this->container->get(Media::class);
        $this->menuRepo = $this->container->get(Menu::class);

        return $this->createDefaultDBConnection(self::$pdo, $this->config->get("db_name"));
    }

    public function getDataSet()
    {
        return new YamlDataSet(__dir__ . "/testsDataSet.yml");
    }
}
