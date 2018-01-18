<?php

namespace App\Controllers;

use App\Config;
use App\Database;
use App\Messages;
use App\Validator;

class Install extends BaseController
{
    public function checkCanInstall(): bool
    {
        $ok = true;
        if (! is_writable(Config::$configFolder)) {
            $ok = false;
            Messages::addError("config.foldernotwritable");
        }

        if (! is_readable(Config::$configFolder . "config.sample.json")) {
            $ok = false;
            Messages::addError("config.samplefilenotreadable");
        }

        if (! is_readable(Database::$dbStructureFile)) {
            $ok = false;
            Messages::addError("install.dbfilenotreadable");
        }

        return $ok;
    }

    public function getInstall()
    {
        $this->checkCanInstall();
        $this->render("install");
    }

    public function postInstall()
    {
        $userPost = Validator::sanitizePost([
            "name" => "string",
            "email" => "string",
            "password" => "string",
            "password_confirm" => "string"
        ]);

        $configPost = Validator::sanitizePost([
            "site_title" => "string",
            "db_host" => "string",
            "db_name" => "string",
            "db_user" => "string",
            "db_password" => "string"
        ]);

        if ($this->checkCanInstall()) {
            if (Validator::csrf("install")) {
                if (Validator::user($userPost) && Database::install($configPost, $userPost)) {
                    // DB OK, just create config file
                    $defaultConfig = file_get_contents(Config::$configFolder . "config.sample.json");
                    $defaultConfig = json_decode($defaultConfig, true);

                    Config::$config = array_merge($defaultConfig, $configPost);
                    Config::save();
                }
            } else {
                Messages::addError("csrffail");
            }
        }

        $data = [
            "post" => array_merge($userPost, $configPost),
        ];
        $this->render("install", null, $data);
    }
}
