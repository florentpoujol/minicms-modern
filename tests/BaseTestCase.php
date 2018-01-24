<?php

namespace Tests;

use App\App;
use App\Config;
use App\Helpers;
use App\Lang;
use App\Renderer;
use App\Session;
use App\Validator;
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

    protected function setUp()
    {
        $this->container = new DIContainer();
        App::$container = $this->container;

        $this->config = new Config(__dir__ . "/testsConfig.json");
        $this->config->load();
        $this->container->set(Config::class, $this->config);

        $this->session = $this->container->get(Session::class);
        $this->helpers = $this->container->get(Helpers::class);
        $this->validator = $this->container->get(Validator::class);

        $this->lang = $this->container->get(Lang::class);
        $this->lang->load("en");

        $this->renderer = $this->container->get(Renderer::class);
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
}