<?php

namespace Tests;

use App\Entities\Category;
use App\Entities\Post;

class CategoryTest extends DatabaseTestCase
{
    function testGet()
    {
        $category = $this->categoryRepo->get(["slug" => "category-1"]);
        $this->assertInstanceOf(Category::class, $category);

        $posts = $category->getPosts();
        $this->assertCount(1, $posts);
        $this->assertContainsOnlyInstancesOf(Post::class, $posts);

        $categories = $this->categoryRepo->getAll();
        $this->assertContainsOnlyInstancesOf(Category::class, $categories);
        $this->assertCount(2, $categories);
        $this->assertEquals(2, $this->categoryRepo->countAll());
    }

    function testCreate()
    {
        $this->assertEquals(2, $this->categoryRepo->countAll());

        $data = [
            "slug" => "category3",
            "title" => "Category 3",
        ];
        $category = $this->categoryRepo->create($data);
        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals("category3", $category->slug);
        $this->assertEquals(3, $this->categoryRepo->countAll());
    }

    function testUpdate()
    {
        $category = $this->categoryRepo->get(1);
        $this->assertEquals("Category 1", $category->title);

        $this->assertTrue($category->update(["title" => "NewCategoryName"]));
        $this->assertEquals("NewCategoryName", $category->title);
        $this->assertEquals("NewCategoryName", $this->categoryRepo->get(1)->title);
    }

    function testDelete()
    {
        $category = $this->categoryRepo->get(1);
        $this->assertEquals("category-1", $category->slug);
        $posts = $category->getPosts();
        $this->assertCount(1, $posts);

        $this->assertTrue($category->delete());

        // $this->>assertNull($category->slug);
        $this->assertCount(0, $category->getPosts());

        $this->assertFalse($this->postRepo->get($posts[0]->id));
    }
}
