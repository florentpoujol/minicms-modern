<?php

namespace App\Controllers\Admin;

use App\Entities\Comment;
use App\Messages;
use App\Route;
use App\Validate;

class Comments extends AdminBaseController
{
    public function getRead($pageNumber = 1)
    {
        $params = ["pageNumber" => $pageNumber];
        $allRows = [];
        $count = 0;
        $userId = $this->user->id;

        if ($this->user->isAdmin()) {
            $allRows = Comment::getAll($params);
            $count = Comment::countAll();
        } elseif ($this->user->isWriter()) {
            $allRows = Comment::getAllForWriter($userId, $pageNumber);
            $count = count(Comment::getAllForWriter($userId));
        } else if ($this->user->isCommenter()) {
            $params["user_id"] = $userId;
            $allRows = Comment::getAll($params);
            $count = Comment::countAll(["user_id" => $userId]);
        }

        $data = [
            "allRows" => $allRows,
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => $count,
                "queryString" => Route::buildQueryString("admin/comments/read")
            ]
        ];
        $this->render("comments.read", "admin.comment.readtitle", $data);
    }

    public function getUpdate($id)
    {
        $comment = Comment::get($id);
        if ($comment === false) {
            Messages::addError("comment.unknown");
            Route::redirect("admin/comments/read");
        }

        $data = [
            "action" => "update",
            "post" => $comment->toArray()
        ];
        $this->render("comments.update", "admin.comment.updatetitle", $data);
    }

    public function postUpdate()
    {
        $post = Validate::sanitizePost([
            "id" => "int",
            "content" => "string",
            "user_id" => "int"
        ]);

        if (Validate::csrf("commentupdate")) {
            if (Validate::comment($post)) {
                $comment = Comment::get($post["id"]);

                if (is_object($comment)) {
                    if ($comment->update($post)) {
                        Messages::addSuccess("comment.updated");
                        Route::redirect("admin/comments/update/".$comment->id);
                    } else {
                        Messages::addError("db.commentupdated");
                    }
                } else {
                    Messages::addError("comment.unknown");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        $post["creation_datetime"] = Comment::get($post["id"])->creation_datetime;

        $data = [
            "action" => "update",
            "post" => $post
        ];
        $this->render("pages.update", "admin.page.updatetitle", $data);
    }

    public function postDelete()
    {
        $id = (int)$_POST["id"];
        if (Validate::csrf("commentdelete$id")) {
            $comment = Comment::get($id);
            if (is_object($comment)) {
                if ($comment->delete()) {
                    Messages::addSuccess("comment.deleted");
                } else {
                    Messages::addError("comment.deleting");
                }
            } else {
                Messages::addError("comment.unknown");
            }
        } else {
            Messages::addError("csrffail");
        }

        Route::redirect("admin/comments/read");
    }
}
