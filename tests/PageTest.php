<?php

use App\Entities\Page;
use App\Entities\User;
use App\Entities\Comment;

class PageTest extends DatabaseTestCase
{
    public function testGet()
    {
        self::assertFalse(Page::get(["id" => 999]));

        $page = Page::get(1);
        self::assertInstanceOf(Page::class, $page);
        self::assertEquals("page1", $page->slug);
    }

    public function testGetResources()
    {
        $page = Page::get(1);

        $children = $page->getChildren();
        self::assertCount(1, $children);
        self::assertContainsOnlyInstancesOf(Page::class, $children);
        self::assertEquals("otherpage", $children[0]->slug);
        self::assertEquals(1, $children[0]->parent_page_id);

        self::assertInstanceOf(Page::class, $children[0]->getParent());
        self::assertEquals(1, $children[0]->getParent()->id);

        $comments = $page->getComments();
        self::assertContainsOnlyInstancesOf(Comment::class, $comments);
        self::assertCount(1, $comments);
    }

    function testCreate()
    {
        self::assertEquals(2, Page::countAll());
        $user = User::get(2);
        self::assertCount(2, $user->getPages());

        $data = [
            "slug" => "page3",
            "title" => "the third page",
            "content" => "the third page content",
            "user_id" => 2,
            "published" => 0,
            "allow_comments" => 1
        ];

        $page = Page::create($data);
        self::assertInstanceOf(Page::class, $page);
        self::assertEquals(3, Page::countAll());
        self::assertCount(3, $user->getPages());
        self::assertEquals(3, $page->id);
        self::assertEquals("the third page content", $page->content);
    }

    public function testUpdate()
    {
        $page = Page::get(1);

        self::assertEquals("the content of the page", $page->content);
        self::assertTrue($page->allowComments());
        $newData = [
            "content" => "the new content",
            "allow_comments" => 0
        ];
        self::assertTrue($page->update($newData));
        self::assertEquals("the new content", $page->content);
        self::assertFalse($page->allowComments());
    }

    public function testDelete()
    {
        $data = [
            "slug" => "page3",
            "title" => "the third page",
            "content" => "the third page content",
            "user_id" => 2,
            "parent_page_id" => 1,
            "published" => 0,
            "allow_comments" => 1
        ];
        $page3 = Page::create($data);
        $page = Page::get(1);
        $user = User::get(2);

        self::assertCount(3, $user->getPages());
        self::assertEquals(1, $page->id);
        self::assertCount(2, $page->getChildren());
        self::assertInstanceOf(Page::class, $page3->getParent());
        self::assertEquals(1, $page3->parent_page_id);
        self::assertEquals(1, $page3->getParent()->id);
        self::assertCount(1, $page->getComments());
        $comment = $page->getComments()[0];

        self::assertTrue($page->delete());
        $page3 = Page::get(3);

        self::assertCount(2, $user->getPages());
        self::assertNull($page->id);
        self::assertNull($page3->parent_page_id);
        self::assertFalse($page3->getParent());
        self::assertCount(0, $page->getChildren());
        self::assertCount(0, $page->getComments());
        self::assertCount(0, Comment::getAll(["page_id" => 1]));
        self::assertFalse(Comment::get($comment->id));
    }
}
