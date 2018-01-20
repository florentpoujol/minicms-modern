<?php

namespace Tests;

use App\Entities\Comment;
use App\Entities\Page;
use App\Entities\Post;
use App\Entities\User;

class CommentTest extends DatabaseTestCase
{
    public function testGet()
    {
        self::assertFalse($this->commentRepo->get(["id" => 999]));

        $comment = $this->commentRepo->get(["id" => 1]);
        self::assertInstanceOf(Comment::class, $comment);
        self::assertEquals("the comment on first post", $comment->content);
        self::assertEquals(1, $comment->id);
        self::assertEquals(3, $comment->user_id);

        $comment = $this->commentRepo->get(["user_id" => 2]);
        self::assertInstanceOf(Comment::class, $comment);
        self::assertEquals(2, $comment->user_id);

        $comments = $this->commentRepo->getAll();
        self::assertInternalType("array", $comments);
        self::assertContainsOnlyInstancesOf(Comment::class, $comments);
        self::assertCount(3, $comments);

        $comments = $this->commentRepo->getAll(["user_id" => 3]);
        self::assertCount(2, $comments);

        self::assertEquals(3, $this->commentRepo->countAll());
    }

    public function testGetResources()
    {
        $comment = $this->commentRepo->get(["id" => 1]);
        $user = $comment->getUser();
        self::assertInstanceOf(User::class, $user);
        $post = $comment->getPost();
        self::assertInstanceOf(Post::class, $post);
        self::assertFalse($comment->getPage());

        $comment = $this->commentRepo->get(["id" => 2]);
        $sameUser = $comment->getUser();
        self::assertInstanceOf(User::class, $sameUser);
        self::assertEquals($user, $sameUser);
        $page = $comment->getPage();
        self::assertInstanceOf(Page::class, $page);
    }

    public function testCreate()
    {
        $user = $this->userRepo->get(["id" => 2]);
        $userComments = $user->getComments();

        $page = $this->pageRepo->get(["id" => 1]);
        $pageComments = $page->getComments();

        $commentCount = $this->commentRepo->countAll();

        $newComment = [
            "user_id" => 2,
            "page_id" => 1,
            "content" => "the comment text"
        ];
        $comment = $this->commentRepo->create($newComment);
        self::assertInstanceOf(Comment::class, $comment);
        self::assertEquals(2, $comment->user_id);
        self::assertEquals(1, $comment->getPage()->id);

        self::assertGreaterThan($commentCount, $this->commentRepo->countAll());
        self::assertGreaterThan(count($pageComments), count($page->getComments()));
        self::assertGreaterThan(count($userComments), count($user->getComments()));
    }

    public function testDelete()
    {
        $comments = $this->commentRepo->getAll(["user_id" => 3]);
        self::assertCount(2, $comments);
        $comment = $comments[0];
        self::assertEquals(1, $comment->id);

        self::assertTrue($this->commentRepo->delete($comment));

        // self::assertNull($comment->id);
        $user = $this->userRepo->get(["id" => 3]);
        self::assertCount(1, $user->getComments());
    }
}
