<?php

use App\Entities\Comment;
use App\Entities\Page;
use App\Entities\Post;
use App\Entities\User;

class CommentTest extends DatabaseTestCase
{
    public function getDataSet()
    {
        return new \PHPUnit\DbUnit\DataSet\YamlDataSet(__dir__."/userDataSet.yml");
    }

    public function testGet()
    {
        self::assertFalse(Comment::get(["id" => 999]));

        $comment = Comment::get(["id" => 1]);
        self::assertInstanceOf(Comment::class, $comment);
        self::assertEquals("the comment on first post", $comment->content);
        self::assertEquals(1, $comment->id);
        self::assertEquals(3, $comment->user_id);

        $comment = Comment::get(["user_id" => 2]);
        self::assertInstanceOf(Comment::class, $comment);
        self::assertEquals(2, $comment->user_id);

        $comments = Comment::getAll();
        self::assertInternalType("array", $comments);
        self::assertContainsOnlyInstancesOf(Comment::class, $comments);
        self::assertCount(3, $comments);

        $comments = Comment::getAll(["user_id" => 3]);
        self::assertCount(2, $comments);

        self::assertEquals(3, Comment::countAll());
    }

    public function testGetResources()
    {
        $comment = Comment::get(["id" => 1]);
        $user = $comment->getUser();
        self::assertInstanceOf(User::class, $user);
        $post = $comment->getPost();
        self::assertInstanceOf(Post::class, $post);
        self::assertFalse($comment->getPage());

        $comment = Comment::get(["id" => 2]);
        $sameUser = $comment->getUser();
        self::assertInstanceOf(User::class, $sameUser);
        self::assertEquals($user, $sameUser);
        $page = $comment->getPage();
        self::assertInstanceOf(Page::class, $page);
    }

    public function testCreate()
    {
        $user = User::get(["id" => 2]);
        $userComments = $user->getComments();
        $pageComments = Page::get(["id" => 1])->getComments();
        $count = Comment::countAll();

        $newComment = [
            "user_id" => 2,
            "page_id" => 1,
            "content" => "the comment text"
        ];

        $comment = Comment::create($newComment);
        self::assertInstanceOf(Comment::class, $comment);
        self::assertEquals(2, $comment->user_id);
        self::assertEquals(1, $comment->getPage()->id);

        self::assertGreaterThan(count($userComments), count($user->getComments()));
        self::assertGreaterThan(count($pageComments), count(Page::get(["id" => 1])->getComments()));
        self::assertGreaterThan($count, Comment::countAll());
    }


    public function testDelete()
    {
        $comments = Comment::getAll(["user_id" => 3]);
        self::assertCount(2, $comments);
        $comment = $comments[0];
        self::assertEquals(1, $comment->id);

        self::assertTrue($comment->delete());

        self::assertNull($comment->id);
        self::assertCount(1, User::get(["id" => 3])->getComments());
    }
}
