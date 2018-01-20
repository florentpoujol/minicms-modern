<?php

namespace App\Controllers\Admin;

use App\Emails;
use App\Entities\User;
use App\Router;
use App\Config as AppConfig;
use App\Validator;

class Config extends AdminBaseController
{
    public function __construct(User $user)
    {
        parent::__construct($user);
        if (! $this->user->isAdmin()) {
            Router::redirect("admin");
        }

        if (! is_writable(AppConfig::$configFolder."config.json")) {
            Messages::addError("Config file not writable !");
        }
    }

    public function getUpdate()
    {
        $data = [
            "config" => AppConfig::$config,
        ];
        $this->render("config", "admin.config.title", $data);
    }

    public function postUpdate()
    {
        $testEmail = Validator::sanitizePost([
            "test_email" => "string",
            "test_email_submit" => "string"
        ]);
        $config = Validator::sanitizePost([
            "db_host" => "string",
            "db_name" => "string",
            "db_user" =>  "string",
            "db_password" => "string",
            "mailer_from_address" =>  "string",
            "mailer_from_name" => "string",
            "smtp_host" => "string",
            "smtp_user" => "string",
            "smtp_password" => "string",
            "smtp_port" => "int",
            "site_title" => "string",
            "recaptcha_secret" => "string",
            "use_nice_url" => "bool",
            "allow_comments" => "bool",
            "allow_registration" => "bool",
            "items_per_page" => "int"
        ]);

        if (Validator::csrf("config")) {
            if ($testEmail["test_email_submit"] !== "") {
                $email = $testEmail["test_email"];
                if (Validator::email($email)) {
                    if (Emails::sendTest($email)) {
                        Messages::addSuccess("Test email sent successfully");
                    }
                } else {
                    Messages::addError("formvalidation.email");
                }
            } else {
                //update config
                if ($config["db_password"] === "") {
                    $config["db_password"] = AppConfig::get("db_password");
                }
                if ($config["smtp_password"] === "") {
                    $config["smtp_password"] = AppConfig::get("smtp_password");
                }

                AppConfig::$config = $config;

                if (AppConfig::save()) {
                    Messages::addSuccess("config.saved");
                    Router::redirect("admin/config");
                } else {
                    Messages::addError("config.save");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        $config["test_email"] = $testEmail["test_email"];
        $data = [
            "config" => $config
        ];
        $this->render("config", "admin.config.title", $data);
    }
}
