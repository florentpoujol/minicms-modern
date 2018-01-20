<?php

namespace Tests\Controllers;

use App\Controllers\Blog;
use Tests\DatabaseTestCase;

class BlogTest extends DatabaseTestCase
{
    public function testBlog()
    {
        $controller = $this->container->make(Blog::class);
        $content = $this->getControllerOutput($controller, "getBlog");
        var_dump($content);
    }
}
