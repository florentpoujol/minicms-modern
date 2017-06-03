<?php

namespace App;

require_once "../App/autoload.php";

App::load();

Config::load();

Models\Model::connect();

Lang::load(Lang::$currentLanguage); // let's imagine $currentLanguage has been changed based on config value

Messages::load();

require_once "../vendor/phpmailer/class.smtp.php"; // no need for autoloading with such a minimal installation
require_once "../vendor/phpmailer/class.phpmailer.php";

// check if user is logged in
session_start();
$user = null; // is App\Entities\User when user is logged in ; null when user is guest
$userId = Session::get("minicms_mvc_auth");

if ($userId !== null) {
    $dbUser = Models\Users::get(["id" => (int)$userId]);

    if ($dbUser === false) {
        Route::logout();
    }

    $user = new Entities\User($dbUser);
}

Route::load($user);
