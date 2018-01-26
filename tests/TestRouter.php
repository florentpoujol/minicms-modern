<?php

namespace Tests;

use App\Router;

class TestRouter extends Router
{
    public $redirectRoute = "";

    public function redirect(string $route = "")
    {
        $this->redirectRoute = $route;
    }
}
