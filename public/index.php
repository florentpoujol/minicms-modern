<?php

namespace App;

require_once "../App/autoload.php";

App::load();

Config::load();

Database::connect();

Lang::load(Lang::$currentLanguage); // let's imagine $currentLanguage has been changed based on config value

Messages::load();

require_once "../vendor/phpmailer/class.smtp.php"; // no need for autoloading with such a minimal installation
require_once "../vendor/phpmailer/class.phpmailer.php";

// check if user is logged in
session_start();
$user = null; // is App\Entities\User when user is logged in ; null when user is guest
$userId = Session::get("minicms_mvc_auth");

if ($userId !== null) {
    $user = Entities\User::get(["id" => (int)$userId]);

    if ($user === false) {
        Route::logout();
    }
}

Route::load($user);
