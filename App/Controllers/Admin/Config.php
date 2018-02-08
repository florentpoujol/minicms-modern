<?php

namespace App\Controllers\Admin;

use App\Entities\User;
use App\Form;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Config as AppConfig;
use App\Session;
use App\Validator;
use App\Mailer;

class Config extends AdminBaseController
{
    /**
     * @var Mailer
     */
    protected $mailer;

    protected $configSchema = [
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
        "recaptcha_site_key" => "string",
        "use_nice_url" => "checkbox",
        "allow_comments" => "checkbox",
        "allow_registration" => "checkbox",
        "items_per_page" => "int",
    ];

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, AppConfig $config, Form $form,
        Mailer $mailer)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config, $form);
        $this->mailer = $mailer;
    }

    public function setLoggedInUser(User $user)
    {
        if (! $user->isAdmin()) {
            $this->router->redirect("admin");
            return;
        }
        parent::setLoggedInUser($user);
    }

    protected function ensureConfigFolderIsWritable()
    {
        if (! is_writable($this->config->getConfigFilePath())) {
            $this->session->addError("Config file not writable !");
        }
    }

    public function getUpdate()
    {
        $this->ensureConfigFolderIsWritable();

        $config = [];
        foreach ($this->configSchema as $configKey => $type) {
            $config[$configKey] = $this->config->get($configKey);
        }

        $data = [
            "configArray" => $config, // there already is the App\Config instance set in the $config variable in the views
        ];
        $this->render("config", $data);
    }

    public function postUpdate()
    {
        $this->ensureConfigFolderIsWritable();

        $testEmail = $this->validator->sanitizePost([
            "test_email" => "string",
            "test_email_submit" => "string",
        ]);

        $config = $this->validator->sanitizePost($this->configSchema);

        if ($this->validator->csrf("config")) {
            if ($testEmail["test_email_submit"] !== "") {
                $email = $testEmail["test_email"];
                if ($this->validator->email($email)) {
                    if ($this->mailer->sendTest($email)) {
                        $this->session->addSuccess("Test email sent successfully");
                    }
                } else {
                    $this->session->addError("formvalidation.email");
                }
            } else {
                //update config
                if ($config["db_password"] === "") {
                    $config["db_password"] = $this->config->get("db_password");
                }
                if ($config["smtp_password"] === "") {
                    $config["smtp_password"] = $this->config->get("smtp_password");
                }

                foreach ($this->configSchema as $configKey => $type) {
                    $this->config->set($configKey, $config[$configKey]);
                }

                if ($this->config->save()) {
                    // note: saving will add the upload_path, site_directory and site_url to the config file
                    // this is ok since these values are never read from the config but generated in App::__construct()
                    $this->session->addSuccess("config.saved");
                    $this->router->redirect("admin/config");
                    return;
                } else {
                    $this->session->addError("config.save");
                }
            }
        } else {
            $this->session->addError("csrffail");
        }

        $config["test_email"] = $testEmail["test_email"];
        $data = [
            "configArray" => $config,
        ];
        $this->render("config", $data);
    }
}
