<?php

namespace Tests\Controllers;

use App\Controllers\Page;
use Tests\DatabaseTestCase;

class PageTest extends DatabaseTestCase
{
    public function testGetPage()
    {
        $controller = $this->container->make(Page::class);

        $page = $this->pageRepo->get(1);

        $content = $this->getControllerOutput($controller, "getPage", $page->id);

        $this->assertContains($page->title, $content);
        $this->assertContains($page->content, $content);

        $this->assertTrue($this->config->get("allow_comments"));
        $this->assertTrue($page->allowComments());
        $this->assertContains($this->lang->get("comment.plural"), $content);

        $comments = $page->getComments();
        $this->assertNotEmpty($comments);
        foreach ($comments as $comment) {
            $this->assertContains($comment->content, $content);
        }

        $this->assertNotContains("<form", $content); // user not logged in, no form to page a new comment
        $this->assertNotContains("submit", $content);
    }

    function testRedirectWrongPostId()
    {
        $controller = $this->container->make(Page::class);
        $content = $this->getControllerOutput($controller, "getPage", 987);
        $this->assertEmpty(trim($content));
        $this->assertContains("page.unknown", $this->session->getErrors());
        $this->assertRedirectTo("blog");
    }

    function testCommentNotAllowed()
    {
        $controller = $this->container->make(Page::class);

        $this->config->set("allow_comments", false);
        $content = $this->getControllerOutput($controller, "getPage", 1);

        $this->assertNotContains($this->lang->get("comment.plural"), $content);
        $comments = $this->commentRepo->getAll(["page_id" => 1]);
        $this->assertNotEmpty($comments);
        foreach ($comments as $comment) {
            $this->assertNotContains($comment->content, $content);
        }


        $page = $this->pageRepo->get(1);
        $page->update(["allow_comments" => 0]);
        $page = $this->pageRepo->get(1);

        $this->assertFalse($page->allowComments());
        $this->config->set("allow_comments", false);
        $this->assertFalse($this->config->get("allow_comments"));
        $comments = $this->commentRepo->getAll(["page_id" => 1]);
        $this->assertNotEmpty($comments);

        $content = $this->getControllerOutput($controller, "getPage", 1);
        $this->assertNotContains($this->lang->get("comment.plural"), $content);
        foreach ($comments as $comment) {
            $this->assertNotContains($comment->content, $content);
        }
    }

    public function testFormComment()
    {
        $user = $this->userRepo->get(1);

        $controller = $this->container->make(Page::class);
        $controller->setLoggedInUser($user);

        $content = $this->getControllerOutput($controller, "getPage", 1);
        $this->assertContains($this->lang->get("comment.plural"), $content);
        $this->assertContains("<form", $content); // user not logged in, no form to page a new comment
        $this->assertContains("submit", $content);
    }

    public function testPageComment()
    {
        $user = $this->userRepo->get(1);
        $page = $this->pageRepo->get(1);
        $oldComments = $page->getComments();


        $controller = $this->container->make(Page::class);
        $controller->setLoggedInUser($user);

        $_POST["content"] = "the content of the new comment";

        $content = $this->getControllerOutput($controller, "postPage", 1);
        $this->assertContains("csrffail", $content);

        // -------------

        $token = "comment_create_" . $user->id . "_" . $page->id;
        $_POST[$token . "_csrf_token"] = $this->session->createCSRFToken($token);

        $content = $this->getControllerOutput($controller, "postPage", 1);

        $newComments = $page->getComments();
        $this->assertNotSame(count($oldComments), count($newComments));
        $lastComment = $newComments[count($newComments) - 1];

        $this->assertSame("the content of the new comment", $lastComment->content);
        $this->assertSame($user->id, $lastComment->user_id);
        $this->assertSame($page->id, $lastComment->page_id);
        $this->assertSame(null, $lastComment->post_id);

        $this->assertContains($lastComment->content, $content);
    }

    function testPostPageRedirectUserNotLoggedIn()
    {
        $controller = $this->container->make(Page::class);
        // no user logged in
        $content = $this->getControllerOutput($controller, "postPage", 1);

        $this->assertEmpty(trim($content));
        $this->assertContains("user.mustbeloggedintopostcomment", $this->session->getErrors());
        $this->assertRedirectTo("page/1");
    }

    function testPostPageRedirectUnknownId()
    {
        $controller = $this->container->make(Page::class);
        $controller->setLoggedInUser($this->userRepo->get(1));
        $content = $this->getControllerOutput($controller, "postPage", 987);

        $this->assertEmpty(trim($content));
        $this->assertContains("page.unknown", $this->session->getErrors());
        $this->assertRedirectTo("blog");
    }
}
