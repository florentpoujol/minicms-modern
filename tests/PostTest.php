<?php

use App\Entities\Post;
use App\Entities\Category;
use App\Entities\User;
use App\Entities\Comment;

class PostTest extends DatabaseTestCase
{
    public function testGet()
    {
        self::assertFalse(Post::get(["id" => 999]));

        $post = Post::get(1);
        self::assertInstanceOf(Post::class, $post);
        self::assertEquals("first-post", $post->slug);
    }

    public function testGetResources()
    {
        $post = Post::get(1);

        $cat = $post->getCategory();
        self::assertInstanceOf(Category::class, $cat);
        self::assertEquals("category-1", $cat->slug);

        $comments = $post->getComments();
        self::assertContainsOnlyInstancesOf(Comment::class, $comments);
        self::assertCount(2, $comments);
    }

    function testCreate()
    {
        self::assertEquals(2, Post::countAll());
        $cat = Category::get(1);
        self::assertCount(1, $cat->getPosts());
        $user = User::get(1);
        self::assertCount(2, $user->getPosts());

        $data = [
            "slug" => "third-post",
            "title" => "the third post",
            "content" => "the third post content",
            "user_id" => 1,
            "category_id" => 1,
            "published" => 0,
            "allow_comments" => 1
        ];

        $post = Post::create($data);
        self::assertInstanceOf(Post::class, $post);
        self::assertEquals(3, Post::countAll());
        self::assertCount(2, $cat->getPosts());
        self::assertCount(3, $user->getPosts());
        self::assertEquals(3, $post->id);
        self::assertEquals("the third post content", $post->content);
    }

    public function testUpdate()
    {
        $post = Post::get(1);

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
        $post = Post::get(1);
        $user = User::get(1);
        $cat = Category::get(1);

        self::assertCount(2, $user->getPosts());
        self::assertEquals(1, $post->id);
        self::assertCount(1, $cat->getPosts());
        self::assertCount(2, $post->getComments());

        self::assertTrue($post->delete());

        self::assertCount(1, $user->getPosts());
        self::assertNull($post->id);
        self::assertCount(0, $cat->getPosts());
        self::assertEmpty($post->getComments());
        self::assertEmpty(Comment::getAll(["post_id" => 1]));
    }
}
