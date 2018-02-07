<?php

namespace App;

use App\Entities\Repositories\User;
use StdCmp\DI\DIContainer;

require_once __dir__ . "/../vendor/autoload.php";

// start setup DI container
$container = new DIContainer();
$container->set(DIContainer::class, $container);

$lang = $container->get(Lang::class);
$lang->load("en"); // let's imagine "en" has been changed based on config value or navigator language

$config = $container->get(Config::class);
$config->load();

$app = $container->get(App::class);
$app::$container = $container; // used in Entity::createHydrated()

$session = $container->get(Session::class);
$session->start();

$router = $container->get(Router::class);
// end setup container

if (!$config->fileExists()) {
    $queryStr = $_SERVER["QUERY_STRING"] ?? "";
    if (trim($queryStr) !== "") {
        $router->redirect();
    }
    $router->toInstall();
    exit;
}

$db = $container->get(Database::class);
$db->connect();

// check if user is logged in
/**
 * Is App\Entities\User when user is logged in ; null when user is guest
 * @var Entities\User|null
 */
$user = null;
$userId = $session->get("minicms_modern_auth");

if ($userId !== null) {
    $userRepo = $container->get(User::class);
    $user = $userRepo->get(["id" => (int)$userId]);

    if ($user === false) {
        $router->logout();
    }
}

$router->load($user);
