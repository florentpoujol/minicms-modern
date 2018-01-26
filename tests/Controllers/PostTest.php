<?php

namespace Tests\Controllers;

use App\Controllers\Post;
use Tests\DatabaseTestCase;

class PostTest extends DatabaseTestCase
{
    public function testGetPost()
    {
        $controller = $this->container->make(Post::class);

        $post = $this->postRepo->get(1);

        $content = $this->getControllerOutput($controller, "getPost", $post->id);

        $this->assertContains($post->title, $content);
        $this->assertContains($post->content, $content);
        $this->assertContains($post->getUser()->name, $content);
        $this->assertContains($post->getCategory()->title, $content);
        $this->assertContains($post->getUser()->name, $content);

        $this->assertTrue($this->config->get("allow_comments"));
        $this->assertTrue($post->allowComments());
        $this->assertContains($this->lang->get("comment.plural"), $content);

        $comments = $post->getComments();
        $this->assertNotEmpty($comments);
        foreach ($comments as $comment) {
            $this->assertContains($comment->content, $content);
        }

        $this->assertNotContains("<form", $content); // user not logged in, no form to post a new comment
        $this->assertNotContains("submit", $content);
    }

    function testGetPostRedirectUnknownId()
    {
        $controller = $this->container->make(Post::class);
        $content = $this->getControllerOutput($controller, "getPost", 987);
        $this->assertEmpty(trim($content));
        $this->assertContains("post.unknown", $this->session->getErrors());
        $this->assertRedirectTo("blog");
    }

    function testCommentNotAllowed()
    {
        $controller = $this->container->make(Post::class);

        $this->config->set("allow_comments", false);
        $content = $this->getControllerOutput($controller, "getPost", 1);

        $this->assertNotContains($this->lang->get("comment.plural"), $content);
        $comments = $this->commentRepo->getAll(["post_id" => 1]);
        $this->assertNotEmpty($comments);
        foreach ($comments as $comment) {
            $this->assertNotContains($comment->content, $content);
        }


        $post = $this->postRepo->get(1);
        $post->update(["allow_comments" => 0]);
        $post = $this->postRepo->get(1);

        $this->assertFalse($post->allowComments());
        $this->config->set("allow_comments", false);
        $this->assertFalse($this->config->get("allow_comments"));
        $comments = $this->commentRepo->getAll(["post_id" => 1]);
        $this->assertNotEmpty($comments);

        $content = $this->getControllerOutput($controller, "getPost", 1);
        $this->assertNotContains($this->lang->get("comment.plural"), $content);
        foreach ($comments as $comment) {
            $this->assertNotContains($comment->content, $content);
        }
    }

    public function testFormComment()
    {
        $user = $this->userRepo->get(1);

        $controller = $this->container->make(Post::class);
        $controller->setLoggedInUser($user);

        $content = $this->getControllerOutput($controller, "getPost", 1);
        $this->assertContains($this->lang->get("comment.plural"), $content);
        $this->assertContains("<form", $content); // user not logged in, no form to post a new comment
        $this->assertContains("submit", $content);
    }

    public function testPostComment()
    {
        $user = $this->userRepo->get(1);
        $post = $this->postRepo->get(1);
        $oldComments = $post->getComments();


        $controller = $this->container->make(Post::class);
        $controller->setLoggedInUser($user);

        $_POST["content"] = "the content of the new comment";

        $content = $this->getControllerOutput($controller, "postPost", 1);
        $this->assertContains("csrffail", $content);

        // -------------

        $this->setupCSRFToken("comment_create_" . $user->id . "_" . $post->id);

        $content = $this->getControllerOutput($controller, "postPost", 1);

        $newComments = $post->getComments();
        $this->assertNotSame(count($oldComments), count($newComments));
        $lastComment = $newComments[count($newComments) - 1];

        $this->assertSame("the content of the new comment", $lastComment->content);
        $this->assertSame($user->id, $lastComment->user_id);
        $this->assertSame($post->id, $lastComment->post_id);
        $this->assertSame(null, $lastComment->page_id);

        $this->assertContains($lastComment->content, $content);
    }

    function testPostPostRedirectUserNotLoggedIn()
    {
        $controller = $this->container->make(Post::class);
        // no user logged in
        $content = $this->getControllerOutput($controller, "postPost", 1);

        $this->assertEmpty(trim($content));
        $this->assertContains("user.mustbeloggedintopostcomment", $this->session->getErrors());
        $this->assertRedirectTo("post/1");
    }

    function testPostPostRedirectUnknownId()
    {
        $controller = $this->container->make(Post::class);
        $controller->setLoggedInUser($this->userRepo->get(1));
        $content = $this->getControllerOutput($controller, "postPost", 987);

        $this->assertEmpty(trim($content));
        $this->assertContains("post.unknown", $this->session->getErrors());
        $this->assertRedirectTo("blog");
    }
}
