<?php

namespace App;

use App\Entities\Entity;
use App\Entities\Repositories\User;
use StdCmp\DI\DIContainer;
use StdCmp\QueryBuilder\QueryBuilder;

require_once __dir__ . "/../vendor/autoload.php";
require __dir__ . "/../../standard-components/vendor/autoload.php"; // todo: remove when the php-standard-component is finally included as a composer dependency here

// start setup DI container
$container = new DIContainer();

$config = $container->get(Config::class);
$config->load();

$lang = $container->get(Lang::class);
$lang->load($lang->currentLanguage); // let's imagine $currentLanguage has been changed based on config value or navigator language

$app = new App($container);
$container->set(App::class, $app);

$session = $container->get(Session::class);

$router = $container->get(Router::class);
// end setup container

if (!$config->fileExists()) {
    $router->toInstall();
    exit;
}

$db = $container->get(Database::class);
$db->connect();

$session->start();


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
