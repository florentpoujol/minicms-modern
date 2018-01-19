<?php

namespace App;

use App\Entities\Entity;
use StdCmp\DI\DIContainer;
use StdCmp\QueryBuilder\QueryBuilder;

require_once __dir__ . "/../vendor/autoload.php";
require __dir__ . "/../../standard-components/vendor/autoload.php"; // todo: remove when the php-standard-component is finally included as a composer dependency here

// start setup DI container
$container = new DIContainer();

$config = $container->get(Config::class);

$localization = $container->get(Lang::class);
$localization->load($localization->currentLanguage); // let's imagine $currentLanguage has been changed based on config value or navigator language

$app = new App($container);
$container->set(App::class, $app);

$session = $container->get(Session::class);
// end setup container

if (!$config->fileExists()) {
    Route::toInstall();
    exit;
}

$db = $container->get(Database::class);
$db->connect();

Entity::$db = $db;
Entity::$config = $config;

$container->set(QueryBuilder::class, function () use ($db) {
    return new QueryBuilder($db->pdo);
});

$session->start();

// check if user is logged in
/**
 * Is App\Entities\User when user is logged in ; null when user is guest
 * @var Entities\User|null
 */
$user = null;
$userId = $session->get("minicms_modern_auth");

if ($userId !== null) {
    $user = Entities\User::get(["id" => (int)$userId]);

    if ($user === false) {
        Route::logout();
    }
}

Route::load($user);
