<?php

use App\Entities\Category;
use App\Entities\Post;

class CategoryTest extends DatabaseTestCase
{
    function testGet()
    {
        $cat = Category::get(["slug" => "category-1"]);
        self::assertInstanceOf(Category::class, $cat);

        $posts = $cat->getPosts();
        self::assertCount(1, $posts);
        self::assertContainsOnlyInstancesOf(Post::class, $posts);

        $cats = Category::getAll();
        self::assertContainsOnlyInstancesOf(Category::class, $cats);
        self::assertCount(2, $cats);
        self::assertEquals(2, Category::countAll());
    }

    function testCreate()
    {
        self::assertEquals(2, Category::countAll());

        $data = [
            "slug" => "category3",
            "title" => "Category 3",
        ];
        $cat = Category::create($data);
        self::assertInstanceOf(Category::class, $cat);
        self::assertEquals("category3", $cat->slug);
        self::assertEquals(3, Category::countAll());
    }

    function testUpdate()
    {
        $cat = Category::get(1);
        self::assertEquals("Category 1", $cat->title);

        self::assertTrue($cat->update(["title" => "NewCategoryName"]));
        self::assertEquals("NewCategoryName", $cat->title);
        self::assertEquals("NewCategoryName", Category::get(1)->title);
    }

    function testDelete()
    {
        $cat = Category::get(1);
        self::assertEquals("category-1", $cat->slug);
        $posts = $cat->getPosts();
        self::assertCount(1, $posts);

        self::assertTrue($cat->delete());

        self::assertNull($cat->slug);
        self::assertCount(0, $cat->getPosts());

        self::assertFalse(Post::get($posts[0]->id));
    }
}
