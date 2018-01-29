<?php

namespace Tests\Controllers;

use App\Config;
use App\Controllers\Install;
use App\Database;
use App\Entities\Repositories\User as UserRepo;
use App\Entities\User;
use org\bovigo\vfs\vfsStream;
use Tests\DatabaseTestCase;

class InstallTest extends DatabaseTestCase
{
    protected $configDirPath = "";

    protected $dbConnexionInfos = [];

    function setUp()
    {
        parent::setUp();

        self::$pdo->exec("DROP DATABASE IF EXISTS `test_minicms_modern_install`;");

        $this->dbConnexionInfos = [
            "db_host" => $this->config->get("db_host"),
            "db_name" => "test_minicms_modern_install",
            "db_user" => $this->config->get("db_user"),
            "db_password" => $this->config->get("db_password"),
        ];

        $root = vfsStream::setup();
        $this->configDirPath = $root->url();
        file_put_contents($root->url() . "/config.sample.json", '{"smtp_host":""}');
        $this->config = new Config($root->url() . "/config.json");

        $this->container->set(Config::class, $this->config);
        $this->container->set(Database::class, $this->container->make(Database::class)); // replace the object in the container by a new one without connection
        $this->database = $this->container->get(Database::class);
        $this->container->set(UserRepo::class, $this->container->make(UserRepo::class));
        $this->userRepo = $this->container->get(UserRepo::class);
    }

    function testCheckCanInstall()
    {
        $controller = $this->container->make(Install::class);
        $this->assertTrue($controller->checkCanInstall());
    }

    function testGetInstall()
    {
        $controller = $this->container->make(Install::class);
        $content = $this->getControllerOutput($controller, "getInstall");

        $this->assertContains("install.pagetitle", $content);
        $this->assertContains("<legend>Website</legend>", $content);

        // file exists
        file_put_contents($this->configDirPath . "/config.json", "");
        $content = $this->getControllerOutput($controller, "getInstall");
        $this->assertRedirectWithError($content, "install.alreadyinstalled", "login");
    }

    function testPostInstallFail()
    {
        $controller = $this->container->make(Install::class);

        // wrong CSRF
        $content = $this->getControllerOutput($controller, "postInstall");
        $this->assertContains("csrffail", $content);

        // wrong user info format
        $this->setupCSRFToken("install");

        $_POST["name"] = "a";
        $_POST["email"] = "a";
        $_POST["password"] = "a";
        $_POST["password_confirm"] = "b";

        $content = $this->getControllerOutput($controller, "postInstall");
        $this->assertContains("fieldvalidation.name", $content);
        $this->assertContains("fieldvalidation.email", $content);
        $this->assertContains("fieldvalidation.password", $content);
        $this->assertContains("fieldvalidation.passwordnotequal", $content);
    }

    function testPostInstallSuccess()
    {
        $controller = $this->container->make(Install::class);

        $_POST["name"] = "FirstAdmin";
        $_POST["email"] = "email@email.fr";
        $_POST["password"] = "Az1";
        $_POST["password_confirmation"] = "Az1";

        $_POST["site_title"] = "New installed site !";
        $_POST = array_merge($_POST, $this->dbConnexionInfos);
        $this->setupCSRFToken("install");

        $content = $this->getControllerOutput($controller, "postInstall");

        $this->assertRedirectWithSuccess($content, "install.success", "login");

        // test actual config file content
        $this->assertFileExists("$this->configDirPath/config.json");
        $strConfig = file_get_contents("$this->configDirPath/config.json");
        $config = json_decode($strConfig, true);
        $this->assertSame($config["site_title"], $_POST["site_title"]);
        $this->assertSame($config["db_name"], $_POST["db_name"]);
        $this->assertSame($config["db_user"], $_POST["db_user"]);
        $this->assertArrayHasKey("smtp_host", $config); // comes from the sample config file

        // test actual DB content
        $this->config->load();
        $this->database = new Database($this->config, $this->session);
        $this->database->connect();
        $this->assertInstanceOf(\PDO::class, $this->database->pdo);
        $this->container->set(Database::class, $this->database);

        $userRepo = $this->container->make(UserRepo::class);
        $user = $userRepo->get(1);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame("FirstAdmin", $user->name);
    }
}
