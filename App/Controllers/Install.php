<?php

namespace App\Controllers;

use App\Config;
use App\Database;
use App\Form;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;
use App\Entities\Repositories\Menu as MenuRepo;
use App\Entities\Repositories\User as UserRepo;

class Install extends BaseController
{
    /**
     * @var Form
     */
    public $form;

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var UserRepo
     */
    public $userRepo;

    /**
     * @var MenuRepo
     */
    public $menuRepo;

    protected $configFilePath = "";
    protected $configDirName = "";

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config,
        Form $form, Database $database, UserRepo $userRepo, MenuRepo $menuRepo)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config);

        $this->form = $form;
        $this->database = $database;
        $this->userRepo = $userRepo;
        $this->menuRepo = $menuRepo;

        $this->configFilePath = $this->config->getConfigFilePath();
        $this->configDirName = dirname($this->configFilePath);
    }

    public function checkCanInstall(): bool
    {
        $ok = true;
        if (! is_writable($this->configDirName)) {
            $ok = false;
            $this->session->addError("config.foldernotwritable");
        }

        if (! is_readable("$this->configDirName/config.sample.json")) {
            $ok = false;
            $this->session->addError("config.samplefilenotreadable");
        }

        if (! is_readable($this->database->dbStructureFile)) {
            $ok = false;
            $this->session->addError("install.dbfilenotreadable");
        }

        return $ok;
    }

    public function getInstall()
    {
        if (file_exists($this->configFilePath)) {
            $this->session->addError("install.alreadyinstalled");
            $this->router->redirect("login");
            return;
        }

        $this->checkCanInstall();
        $data = [
            "form" => $this->form,
            "post" => [],
        ];
        $this->render("install", $data);
    }

    public function postInstall()
    {
        if (file_exists($this->configFilePath)) {
            $this->session->addError("install.alreadyinstalled");
            $this->router->redirect("login");
            return;
        }

        $userPost = $this->validator->sanitizePost([
            "name" => "string",
            "email" => "string",
            "password" => "string",
            "password_confirmation" => "string",
            "email_token" => "string",
        ]);
        $userPost["id"] = -1; // this is needed for Validator::user() not to call Database::valueExistsInDB() at a time where doesn't make sense since no DB is installed

        $configPost = $this->validator->sanitizePost([
            "site_title" => "string",
            "db_host" => "string",
            "db_name" => "string",
            "db_user" => "string",
            "db_password" => "string",
        ]);

        if ($this->checkCanInstall()) {
            if ($this->validator->csrf("install")) {
                if (
                    $this->validator->user($userPost) &&
                    $this->database->install($configPost, $userPost, $this->userRepo, $this->menuRepo)
                ) {
                    // DB OK, just create config file
                    $defaultConfig = file_get_contents("$this->configDirName/config.sample.json");
                    $defaultConfig = json_decode($defaultConfig, true);

                    $jsonConfig = json_encode(array_merge($defaultConfig, $configPost), JSON_PRETTY_PRINT);

                    if (file_put_contents($this->configFilePath, $jsonConfig)) {
                        $this->session->addSuccess("install.success");
                        $this->router->redirect("login");
                        return;
                    } else {
                        $this->session->addError("install.cantwriteconfigfile");
                    }
                }
            } else {
                $this->session->addError("csrffail");
            }
        }

        $data = [
            "form" => $this->form,
            "post" => array_merge($userPost, $configPost),
        ];
        $this->render("install", $data);
    }
}
