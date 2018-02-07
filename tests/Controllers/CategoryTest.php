<?php

namespace Tests\Controllers;

use App\Controllers\Category;
use Tests\DatabaseTestCase;

class CategoryTest extends DatabaseTestCase
{
    public function testGetCategory()
    {
        $this->postRepo->create([
            "title" => "other post on category 1",
            "slug" => "",
            "content" => "",
            "user_id" => 1,
            "category_id" => 1,
        ]);
        $this->postRepo->create([
            "title" => "other post on category 2",
            "slug" => "",
            "content" => "",
            "user_id" => 2,
            "category_id" => 2,
        ]);
        // now there is 2 posts per category

        $controller = $this->container->make(Category::class);

        $categories = $this->categoryRepo->getAll();
        $this->assertNotEmpty($categories);
        foreach ($categories as $category) {
            $content = $this->getControllerOutput($controller, "getCategory", $category->id);

            // $text = $this->lang->get("post.createdbyheader", ["userName" => $user->name, "categoryName" => $category->name]);
            $this->assertContains($category->title, $content);
            $this->assertContains("category.pagetitle", $content);

            $posts = $category->getPosts();
            $this->assertNotEmpty($categories);
            foreach ($posts as $post) {
                $this->assertContains($post->title, $content);
            }
        }
    }

    public function testPagination()
    {
        $this->config->set("items_per_page", 1);

        $this->postRepo->create([
            "title" => "other post on category 1",
            "slug" => "",
            "content" => "",
            "user_id" => 1,
            "category_id" => 1,
        ]);
        $posts = $this->postRepo->getAll(["category_id" => 1]);
        $this->assertNotEmpty($posts);
        $this->assertTrue(count($posts) > $this->config->get("items_per_page"));

        $controller = $this->container->make(Category::class);

        $content = $this->getControllerOutput($controller, "getCategory", 1);
        $this->assertNotEmpty(trim($content));
        $this->assertContains($posts[0]->title, $content);
        $this->assertNotContains($posts[1]->title, $content);

        $content = $this->getControllerOutput($controller, "getCategory", 1, 1);
        $this->assertNotEmpty(trim($content));
        $this->assertContains($posts[0]->title, $content);
        $this->assertNotContains($posts[1]->title, $content);

        $content = $this->getControllerOutput($controller, "getCategory", 1, 2);
        $this->assertNotEmpty(trim($content));
        $this->assertNotContains($posts[0]->title, $content);
        $this->assertContains($posts[1]->title, $content);
        $this->assertRegExp("~<a href=.*>1</a>\n~", $content);
        $this->assertRegExp('~<a href=.* class="current">2</a>~', $content);
    }

    function testRedirectWrongCategory()
    {
        $controller = $this->container->make(Category::class);
        $this->getControllerOutput($controller, "getCategory", 987);

        $this->assertContains("category.unknown", $this->session->getErrors());
        $this->assertRedirectTo("blog");
    }
}
