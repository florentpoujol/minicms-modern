<?php

namespace Tests;

use App\Entities\Post;
use App\Entities\Category;
use App\Entities\Comment;

class PostTest extends DatabaseTestCase
{
    public function testGet()
    {
        self::assertFalse($this->postRepo->get(["id" => 999]));

        $post = $this->postRepo->get(1);
        self::assertInstanceOf(Post::class, $post);
        self::assertEquals("first-post", $post->slug);
    }

    public function testGetResources()
    {
        $post = $this->postRepo->get(1);

        $category = $post->getCategory();
        self::assertInstanceOf(Category::class, $category);
        self::assertEquals("category-1", $category->slug);

        $comments = $post->getComments();
        self::assertContainsOnlyInstancesOf(Comment::class, $comments);
        self::assertCount(2, $comments);
    }

    function testCreate()
    {
        self::assertEquals(2, $this->postRepo->countAll());
        $category = $this->categoryRepo->get(1);
        self::assertCount(1, $category->getPosts());
        $user = $this->userRepo->get(1);
        self::assertCount(1, $user->getPosts());

        $data = [
            "slug" => "third-post",
            "title" => "the third post",
            "content" => "the third post content",
            "user_id" => 1,
            "category_id" => 1,
            "published" => 0,
            "allow_comments" => 1
        ];

        $post = $this->postRepo->create($data);
        self::assertInstanceOf(Post::class, $post);
        self::assertEquals(3, $this->postRepo->countAll());
        self::assertCount(2, $category->getPosts());
        self::assertCount(2, $user->getPosts());
        self::assertEquals(3, $post->id);
        self::assertEquals("the third post content", $post->content);
    }

    public function testUpdate()
    {
        $post = $this->postRepo->get(1);

        self::assertEquals("the content of first post", $post->content);
        self::assertTrue($post->isPublished());
        $newData = [
            "content" => "the new content",
            "published" => 0
        ];
        self::assertTrue($post->update($newData));
        self::assertEquals("the new content", $post->content);
        self::assertFalse($post->isPublished());
    }

    public function testDelete()
    {
        $post = $this->postRepo->get(1);
        $user = $this->userRepo->get(1);
        $category = $this->categoryRepo->get(1);

        self::assertCount(1, $user->getPosts());
        self::assertEquals(1, $post->id);
        self::assertCount(1, $category->getPosts());
        self::assertCount(2, $post->getComments());

        self::assertTrue($this->postRepo->delete($post));

        self::assertCount(1, $user->getPosts());
        // self::assertNull($post->id);
        self::assertCount(0, $category->getPosts());
        self::assertEmpty($post->getComments());
        self::assertEmpty($this->commentRepo->getAll(["post_id" => 1]));
    }
}
