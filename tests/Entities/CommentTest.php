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
        $this->assertFalse($this->commentRepo->get(["id" => 999]));

        $comment = $this->commentRepo->get(["id" => 1]);
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals("the comment on first post", $comment->content);
        $this->assertEquals(1, $comment->id);
        $this->assertEquals(3, $comment->user_id);

        $comment = $this->commentRepo->get(["user_id" => 2]);
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals(2, $comment->user_id);

        $comments = $this->commentRepo->getAll();
        $this->assertInternalType("array", $comments);
        $this->assertContainsOnlyInstancesOf(Comment::class, $comments);
        $this->assertCount(3, $comments);

        $comments = $this->commentRepo->getAll(["user_id" => 3]);
        $this->assertCount(2, $comments);

        $this->assertEquals(3, $this->commentRepo->countAll());
    }

    public function testGetResources()
    {
        $comment = $this->commentRepo->get(["id" => 1]);
        $user = $comment->getUser();
        $this->assertInstanceOf(User::class, $user);
        $post = $comment->getPost();
        $this->assertInstanceOf(Post::class, $post);
        $this->assertFalse($comment->getPage());

        $comment = $this->commentRepo->get(["id" => 2]);
        $sameUser = $comment->getUser();
        $this->assertInstanceOf(User::class, $sameUser);
        $this->assertEquals($user, $sameUser);
        $page = $comment->getPage();
        $this->assertInstanceOf(Page::class, $page);
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
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals(2, $comment->user_id);
        $this->assertEquals(1, $comment->getPage()->id);

        $this->assertGreaterThan($commentCount, $this->commentRepo->countAll());
        $this->assertGreaterThan(count($pageComments), count($page->getComments()));
        $this->assertGreaterThan(count($userComments), count($user->getComments()));
    }

    public function testGetAllForWriter()
    {
        $user = $this->userRepo->get(["role" => "writer"]);
        $userComments = $user->getComments();
        $this->assertSame(1, count($userComments));

        $userPages = array_merge($user->getPages(), $user->getPosts());
        $this->assertSame(2, count($userPages));

        foreach ($userPages as $page) {
            $comments = $page->getComments();
            $userComments = array_merge($userComments, $comments);
            // should add 2 comments of post 1
        }
        $userComments = array_unique($userComments, SORT_REGULAR);

        $allComments = $this->commentRepo->getAllForEditor($user);
        $this->assertSame(count($userComments), count($allComments));
    }

    public function testDelete()
    {
        $comments = $this->commentRepo->getAll(["user_id" => 3]);
        $this->assertCount(2, $comments);
        $comment = $comments[0];
        $this->assertEquals(1, $comment->id);

        $this->assertTrue($this->commentRepo->delete($comment));

        $user = $this->userRepo->get(["id" => 3]);
        $this->assertCount(1, $user->getComments());
    }
}
