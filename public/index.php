<?php

namespace App;

require_once "../app/autoload.php";

App::load();

Config::load();

Lang::load(Lang::$defaultLanguage); // let's imagine it has been change based on config value

Models\Model::connect();

Messages::load();

require_once "../app/helpers.php";

// check if user is logged in
session_start();

$user = null;
$userId = Session::get("minicms_mvc_auth");

if (isset($userId)) {
    $dbUser = Models\Users::get(["id" => (int)$userId]);

    if ($dbUser === false) {
        Route::logout(); // for some reason the logged in user isn't found in the databse... let's log it out, just in case
        // Route::logout();
    }

    $user = new Entities\User($dbUser);
}

require_once "../vendor/phpmailer/class.smtp.php";
require_once "../vendor/phpmailer/class.phpmailer.php";

// first item is default route
$routes = [
    "blog",
    "(page|post|category)/([a-z0-9]+)",
    "logout",
    "login/?(lostpassword|resetpassword)?",
    "register/?(resendconfirmationemail|confirmemail)?",
    "admin/?(users|pages|posts|comments|categories|config|medias)?/?([0-9]+)?/?(create|update|delete)?",
];

Route::load($routes);
