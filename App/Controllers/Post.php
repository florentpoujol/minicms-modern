<?php

namespace App\Controllers;

use App\Entities\Post as PostEntity;
use App\Messages;
use App\Route;

class Post extends BaseController
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
}
