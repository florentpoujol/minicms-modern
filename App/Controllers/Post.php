<?php

namespace App\Controllers;

use App\Entities\Post as PostEntity;
use App\Messages;
use App\Route;

class Post extends Commentable
{
    public function getPost($postId)
    {
        $post = PostEntity::get($postId);

        if ($post === false) {
            Messages::addError("post.unknow");
            Route::redirect("blog");
        }

        $data = [
            "post" => $post
        ];
        $this->render("post", $post->title, $data);
    }

    public function postPost($postId)
    {
        if (! isset($this->user)) {
            Messages::addError("user.mustbeloggedintopostcomment");
            Route::redirect("post/$postId");
        }

        $thePost = PostEntity::get($postId);

        if ($thePost === false) {
            Messages::addError("post.unknow");
            Route::redirect("blog");
        }

        $data = [
            "post" => $thePost,
            "commentPost" => $this->postComment($postId)
        ];
        $this->render("post", $thePost->title, $data);
    }
}
