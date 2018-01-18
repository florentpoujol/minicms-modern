<?php

namespace App;

require_once __dir__ . "/../vendor/autoload.php";

$configLoaded = Config::load();

Lang::load(Lang::$currentLanguage); // let's imagine $currentLanguage has been changed based on config value or navigator language

App::load();

if ($configLoaded) {

    Database::connect();

    session_start();

    Messages::load();

    // check if user is logged in
    /**
     * Is App\Entities\User when user is logged in ; null when user is guest
     * @var Entities\User|null
     */
    $user = null;
    $userId = Session::get("minicms_modern_auth");

    if ($userId !== null) {
        $user = Entities\User::get(["id" => (int)$userId]);

        if ($user === false) {
            Route::logout();
        }
    }

    Route::load($user);
} else {
    Route::toInstall();
}
