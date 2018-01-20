<?php

namespace Tests;

use App\Entities\Category;
use App\Entities\Post;

class CategoryRepoTest extends DatabaseTestCase
{

    function testGet()
    {
        $category = $this->categoryRepo->get(["slug" => "category-1"]);
        self::assertInstanceOf(Category::class, $category);

        $posts = $category->getPosts();
        self::assertCount(1, $posts);
        self::assertContainsOnlyInstancesOf(Post::class, $posts);

        $categories = $this->categoryRepo->getAll();
        self::assertContainsOnlyInstancesOf(Category::class, $categories);
        self::assertCount(2, $categories);
        self::assertEquals(2, $this->categoryRepo->countAll());
    }

    function testCreate()
    {
        self::assertEquals(2, $this->categoryRepo->countAll());

        $data = [
            "slug" => "category3",
            "title" => "Category 3",
        ];
        $category = $this->categoryRepo->create($data);
        self::assertInstanceOf(Category::class, $category);
        self::assertEquals("category3", $category->slug);
        self::assertEquals(3, $this->categoryRepo->countAll());
    }

    function testUpdate()
    {
        $category = $this->categoryRepo->get(1);
        self::assertEquals("Category 1", $category->title);

        self::assertTrue($category->update(["title" => "NewCategoryName"]));
        self::assertEquals("NewCategoryName", $category->title);
        self::assertEquals("NewCategoryName", $this->categoryRepo->get(1)->title);
    }

    function testDelete()
    {
        $category = $this->categoryRepo->get(1);
        self::assertEquals("category-1", $category->slug);
        $posts = $category->getPosts();
        self::assertCount(1, $posts);

        self::assertTrue($category->delete());

        // self::assertNull($category->slug);
        self::assertCount(0, $category->getPosts());

        self::assertFalse($this->postRepo->get($posts[0]->id));
    }
}
