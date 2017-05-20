<?php

namespace App;

require_once "../app/autoload.php";

App::load();

Config::load();

Models\Model::connect();

Messages::load();

require_once "../app/helpers.php";

// check if user is logged in
session_start();

$user = false;

$sessionUserId = Session::get("minicms_poo_auth");
if (isset($sessionUserId)) {
    $user = Models\Users::get(["id" => (int)$sessionUserId]);

    if ($user === false) {
        logout(); // for some reason the logged in user isn't found in the databse... let's log it out, just in case
        // Route::logout();
    }
}

require_once "../vendor/phpmailer/class.smtp.php";
require_once "../vendor/phpmailer/class.phpmailer.php";

// --------------------------------------------------
// routing

$controllerName = isset($_GET["c"]) ? $_GET["c"] : "";
$controllerName .= "Controller";

// var_dump($_SERVER);
// var_dump($_GET);

$action = isset($_GET["a"]) ? $_GET["a"] : "index";

if ($controllerName !== "") {
    if ($controllerName === "logoutController") {
        logout();
    }
    $controllerName = "\App\Controllers\\$controllerName";
    $controller = new $controllerName;
    $controller->{App::$requestMethod.$action}();
}

// Message::saveForLater();
