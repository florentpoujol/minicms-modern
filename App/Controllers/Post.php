<?php

namespace App\Controllers;

use App\Entities\Post as PostEntity;
use App\Messages;
use App\Router;

class Post extends Commentable
{
    public function getPost(int $postId)
    {
        $post = PostEntity::get($postId);

        if ($post === false) {
            Messages::addError("post.unknow");
            Router::redirect("blog");
        }

        $data = [
            "post" => $post
        ];
        $this->render("post", $post->title, $data);
    }

    public function postPost(int $postId)
    {
        if (! isset($this->user)) {
            Messages::addError("user.mustbeloggedintopostcomment");
            Router::redirect("post/$postId");
        }

        $thePost = PostEntity::get($postId);

        if ($thePost === false) {
            Messages::addError("post.unknow");
            Router::redirect("blog");
        }

        $data = [
            "post" => $thePost,
            "commentPost" => $this->postComment($postId)
        ];
        $this->render("post", $thePost->title, $data);
    }
}
