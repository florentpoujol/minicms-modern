<?php

namespace App\Controllers;

use App\Entities\Comment;
use App\Messages;
use App\Validate;

class Commentable extends BaseController
{
    public function postComment($entityId)
    {
        $schema = [
            "content" => "string"
        ];

        $strEntity = "post";
        if (get_called_class() === "App\Controllers\Page") {
            $strEntity = "page";
        }
        $schema[$strEntity."_id"] = "int";

        $post = Validate::sanitizePost($schema);
        $post["user_id"] = $this->user->id;

        if (Validate::csrf("commentcreate".$this->user->id."_$entityId")) {
            if (Validate::comment($post)) {
                $comment = Comment::create($post);

                if (is_object($comment)) {
                    Messages::addSuccess("comment.created");
                    $post = []; // instead of redirecting, let fall back to the view
                } else {
                    Messages::addError("comment.create");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        return $post;
    }
}