<?php

namespace Tests;

use App\Database;
use App\Entities\Entity;
use App\Entities\Repositories\Category;
use App\Entities\Repositories\Comment;
use App\Entities\Repositories\Media;
use App\Entities\Repositories\Menu;
use App\Entities\Repositories\Page;
use App\Entities\Repositories\Post;
use App\Entities\Repositories\User;
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
        $this->pageRepo = $this->container->get(Page::class);
        $this->commentRepo = $this->container->get(Comment::class);
        $this->mediaRepo = $this->container->get(Media::class);
        $this->menuRepo = $this->container->get(Menu::class);
        $this->userRepo = $this->container->get(User::class);

        $this->categoryRepo->postRepo = $this->postRepo;

        $this->postRepo->categoryRepo = $this->categoryRepo;
        $this->postRepo->userRepo = $this->userRepo;
        $this->postRepo->commentRepo = $this->commentRepo;

        $this->pageRepo->userRepo = $this->userRepo;
        $this->pageRepo->commentRepo = $this->commentRepo;

        $this->commentRepo->pageRepo = $this->pageRepo;
        $this->commentRepo->postRepo = $this->postRepo;
        $this->commentRepo->userRepo = $this->userRepo;

        $this->userRepo->postRepo = $this->postRepo;
        $this->userRepo->pageRepo = $this->pageRepo;
        $this->userRepo->commentRepo = $this->commentRepo;
        $this->userRepo->mediaRepo = $this->mediaRepo;

        return $this->createDefaultDBConnection(self::$pdo, $GLOBALS["DB_NAME"]);
    }

    public function getDataSet()
    {
        return new YamlDataSet(__dir__ . "/testsDataSet.yml");
    }
}
