<?php

namespace Tests;

use App\App;
use App\Config;
use App\Helpers;
use App\Lang;
use App\Mailer;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use StdCmp\DI\DIContainer;

abstract class BaseTestCase extends TestCase
{
    /**
     * @var DIContainer
     */
    protected $container;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Lang
     */
    protected $lang;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Helpers
     */
    protected $helpers;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var TestRouter
     */
    protected $router;

    /**
     * @var TestMailer
     */
    protected $mailer;

    protected function setUp()
    {
        $this->container = new DIContainer();
        App::$container = $this->container;

        $root = vfsStream::setup();
        file_put_contents($root->url() . "/en.php", '<?php
        return [
            "post" => [ "createdbyheader" => "Created by {userName} in category {categoryName}." ],
            "messages" => [
                "success" => [
                    "user" => [
                        "loggedin" => "Welcome {username}, you are now logged in",
                    ],
                ],
                "error" => [
                    "user" => [
                        "unknownwithfield" => "Unknow user with {field} \'{value}\'",
                    ],
                ],
            ],
            "email" => [
                "confirmemail" => [ "body" => "{url}" ],
            ],
        ];');
        $this->lang = new Lang($root->url());
        $this->assertTrue($this->lang->load("en"));
        $this->container->set(Lang::class, $this->lang);

        $this->config = new Config(__dir__ . "/testsConfig.json");
        $this->config->load();
        $this->container->set(Config::class, $this->config);

        $this->helpers = $this->container->get(Helpers::class);
        $this->session = $this->container->get(Session::class);

        // depends on Database too, but DatabaseTestCase::getConnection() runs before setup()
        // a correct database instance in already in the container when needed
        $this->validator = $this->container->get(Validator::class);

        $this->container->set(Router::class, TestRouter::class);
        $this->router = $this->container->get(Router::class);

        $this->renderer = $this->container->get(Renderer::class);

        $this->container->set(Mailer::class, TestMailer::class);
        $this->mailer = $this->container->get(Mailer::class);

        $_POST = [];
    }

    protected function getControllerOutput($controller, string $method, ...$args)
    {
        ob_start();
        if (empty($args)) {
            $controller->{$method}();
        } else {
            $controller->{$method}(...$args);
        }
        return ob_get_clean();
    }

    protected function setupCSRFToken(string $requestName)
    {
        $token = $this->session->createCSRFToken($requestName);
        $_POST[$requestName . "_csrf_token"] = $token;
        $_POST[$requestName . "_csrf_time"] = time();
        return $token;
    }

    protected function assertRedirectTo(string $route): bool
    {
        $goodRedirect = $this->router->redirectRoute === $route;
        $this->router->redirectRoute = "";
        return $goodRedirect;
    }

    protected function assertRedirectWithError(string $content, string $error, string $route)
    {
        $this->assertEmpty(trim($content));
        $this->assertContains($error, $this->session->getErrors());
        $this->assertRedirectTo($route);
    }

    protected function assertRedirectWithSuccess(string $content, string $success, string $route)
    {
        $this->assertEmpty(trim($content));
        if ($success !== "") {
            $this->assertContains($success, $this->session->getSuccesses());
        }
        $this->assertRedirectTo($route);
    }
}