<?php

namespace App;

require_once "../autoload.php";

App::load();

Config::load();

Database::connect();

Lang::load(Lang::$currentLanguage); // let's imagine $currentLanguage has been changed based on config value

session_start();

Messages::load();

require_once "../vendor/phpmailer/class.smtp.php"; // no need for autoloading with such a minimal installation
require_once "../vendor/phpmailer/class.phpmailer.php";

// check if user is logged in
/**
 * Is App\Entities\User when user is logged in ; null when user is guest
 * @var Entities\User|null
 */
$user = null;
$userId = Session::get("minicms_mvc_auth");

if ($userId !== null) {
    $user = Entities\User::get(["id" => (int)$userId]);

    if ($user === false) {
        Route::logout();
    }
}

Route::load($user);
