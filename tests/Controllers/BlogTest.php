<?php

namespace Tests;

use App\Controllers\Blog;

class BlogTest extends DatabaseTestCase
{
    public function testBlog()
    {
        $controller = $this->container->make(Blog::class);
        $content = $this->getControllerOutput($controller, "getBlog");

        $posts = $this->postRepo->getAll();
        $this->assertNotEmpty($posts);
        $this->assertTrue(count($posts) < $this->config->get("items_per_page"));
        foreach ($posts as $post) {
            $this->assertContains($post->title, $content);
        }

        $categories = $this->categoryRepo->getAll();
        $this->assertNotEmpty($categories);
        foreach ($categories as $category) {
            $this->assertContains($category->title, $content);
        }
    }

    public function testPagination()
    {
        $this->config->set("items_per_page", 1);
        $posts = $this->postRepo->getAll();
        $this->assertNotEmpty($posts);
        $this->assertTrue(count($posts) > $this->config->get("items_per_page"));

        $controller = $this->container->make(Blog::class);

        $content = $this->getControllerOutput($controller, "getBlog");
        $this->assertNotEmpty(trim($content));
        $this->assertContains($posts[0]->title, $content);
        $this->assertNotContains($posts[1]->title, $content);
        $this->assertRegExp('~<a href=.* class="current">1</a>~', $content);
        $this->assertRegExp("~ <a href=.*>2</a>\n~", $content);

        $content = $this->getControllerOutput($controller, "getBlog", 1);
        $this->assertNotEmpty(trim($content));
        $this->assertContains($posts[0]->title, $content);
        $this->assertNotContains($posts[1]->title, $content);

        $content = $this->getControllerOutput($controller, "getBlog", 2);
        $this->assertNotEmpty(trim($content));
        $this->assertNotContains($posts[0]->title, $content);
        $this->assertContains($posts[1]->title, $content);
        $this->assertRegExp("~<a href=.*>1</a>\n~", $content);
        $this->assertRegExp('~<a href=.* class="current">2</a>~', $content);
    }
}
