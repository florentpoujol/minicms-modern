<?php

namespace Tests;

use App\Entities\Category as Category;

class CategoryTest extends DatabaseTestCase
{

    function testGet()
    {
        $category = $this->categoryRepo->get(["slug" => "category-1"]);
        self::assertInstanceOf(Category::class, $category);

        // $posts = $category->getPosts();
        // self::assertCount(1, $posts);
        // self::assertContainsOnlyInstancesOf(Post::class, $posts);

        $cats = $this->categoryRepo->getAll();
        self::assertContainsOnlyInstancesOf(Category::class, $cats);
        self::assertCount(2, $cats);
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

        self::assertTrue($this->categoryRepo->update($category, ["title" => "NewCategoryName"]));
        self::assertEquals("NewCategoryName", $category->title);
        self::assertEquals("NewCategoryName", $this->categoryRepo->get(1)->title);
    }

    function testDelete()
    {
        $category = $this->categoryRepo->get(1);
        self::assertEquals("category-1", $category->slug);
        $posts = $this->categoryRepo->getPosts($category);
        self::assertCount(1, $posts);

        $categoryId = $category->id;
        self::assertTrue($this->categoryRepo->delete($category));

        // self::assertNull($category->slug);
        self::assertCount(0, $this->categoryRepo->getPosts($category));
        // self::assertCount(0, $this->postRepo->getAll(["category_id" => $categoryId]));

        self::assertFalse($this->postRepo->get($posts[0]->id));
    }
}
