<?php

namespace App;

require_once "../App/autoload.php";

App::load();

Config::load();

Models\Model::connect();

Lang::load(Lang::$defaultLanguage); // let's imagine it has been change based on config value

Messages::load();


// check if user is logged in
session_start();

$user = null;
$userId = (int)Session::get("minicms_mvc_auth");

if (isset($userId)) {
    $dbUser = Models\Users::get(["id" => $userId]);

    if ($dbUser === false) {
        Route::logout();
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
    "admin/?(users|pages|posts|comments|categories|config|medias|menus)?/?([0-9]+)?/?(create|update|delete)?",
];

Route::load($routes);
