<?php

namespace App;

use StdCmp\DI\DIContainer;
use StdCmp\Session\NativeSession;

require_once __dir__ . "/../vendor/autoload.php";

// start setup DI container
$container = new DIContainer();

$config = new Config();
$this->container->set(Config::class, $config);

$localization = new Lang();
$localization->load($localization->currentLanguage); // let's imagine $currentLanguage has been changed based on config value or navigator language
$this->container->set(Lang::class, $localization);

$app = new App($container);
$container->set(App::class, $app);

$session = $container->get(Session::class);
$container->set(Session::class, $session);
// end setup container

if ($config->fileExists()) {
    Database::connect();

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
} else {
    Route::toInstall();
}
