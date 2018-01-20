<?php

namespace Tests;

use App\Entities\Page;
use App\Entities\Comment;

class PageTest extends DatabaseTestCase
{
    public function testGet()
    {
        self::assertFalse($this->pageRepo->get(["id" => 999]));

        $page = $this->pageRepo->get(1);
        self::assertInstanceOf(Page::class, $page);
        self::assertEquals("page1", $page->slug);
    }

    public function testGetResources()
    {
        $page = $this->pageRepo->get(1);

        $children = $page->getChildren();
        self::assertCount(1, $children);
        self::assertContainsOnlyInstancesOf(Page::class, $children);
        self::assertEquals("otherpage", $children[0]->slug);
        self::assertEquals(1, $children[0]->parent_page_id);

        $parent = $children[0]->getParent();
        self::assertInstanceOf(Page::class, $parent);
        self::assertEquals(1, $parent->id);

        $comments = $page->getComments();
        self::assertContainsOnlyInstancesOf(Comment::class, $comments);
        self::assertCount(1, $comments);
    }

    function testCreate()
    {
        self::assertEquals(2, $this->pageRepo->countAll());

        $data = [
            "slug" => "page3",
            "title" => "the third page",
            "content" => "the third page content",
            "published" => 0,
            "user_id" => 2,
            "allow_comments" => 1
        ];

        $page = $this->pageRepo->create($data);
        self::assertInstanceOf(Page::class, $page);
        self::assertEquals(3, $this->pageRepo->countAll());
        self::assertEquals(3, $page->id);
        self::assertEquals("the third page content", $page->content);
    }

    public function testUpdate()
    {
        $page = $this->pageRepo->get(1);

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
            "parent_page_id" => 1,
            "published" => 0,
            "user_id" => 3,
            "allow_comments" => 1
        ];
        $page3 = $this->pageRepo->create($data);
        $page = $this->pageRepo->get(1);

        self::assertEquals(1, $page->id);
        self::assertCount(2, $page->getChildren());
        $page3Parent = $page3->getParent();
        self::assertInstanceOf(Page::class, $page3Parent);
        self::assertEquals(1, $page3->parent_page_id);
        self::assertEquals(1, $page3Parent->id);
        self::assertCount(1, $page->getComments());
        $comment = $page->getComments()[0];

        self::assertTrue($this->pageRepo->delete($page));

        $page3 = $this->pageRepo->get(3);
        $page3Parent = $page3->getParent();

        // self::assertNull($page->id);
        self::assertNull($page3->parent_page_id);
        self::assertNull($page3Parent);
        self::assertCount(0, $page->getChildren());
        self::assertCount(0, $page->getComments());
        self::assertCount(0, $this->commentRepo->getAll(["page_id" => 1]));
    }
}
