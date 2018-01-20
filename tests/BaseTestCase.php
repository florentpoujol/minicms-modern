<?php

namespace Tests;

use App\App;
use App\Config;
use App\Helpers;
use App\Lang;
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

    protected function setUp()
    {
        $this->container = new DIContainer();
        App::$container = $this->container;

        $this->config = $this->container->get(Config::class);
        $this->session = $this->container->get(Session::class);
        $this->helpers = $this->container->get(Helpers::class);
        $this->validator = $this->container->get(Validator::class);

        $this->lang = $this->container->get(Lang::class);
        $this->lang->load("en");
    }
}