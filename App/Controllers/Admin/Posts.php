<?php

namespace App\Controllers\Admin;

use App\Entities\Post;
use App\Messages;
use App\Router;
use App\Validator;

class Posts extends AdminBaseController
{
    public function getRead(int $pageNumber = 1)
    {
        $allRows = Post::getAll(["pageNumber" => $pageNumber]);

        $data = [
            "allRows" => $allRows,
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => Post::countAll(),
                "queryString" => Router::getQueryString("admin/posts/read/")
            ]
        ];
        $this->render("posts.read", "posts.pagetitle", $data);
    }

    public function getCreate()
    {
        $this->render("posts.update", "posts.createnew", ["action" => "create"]);
    }

    public function postCreate()
    {
        $post = Validator::sanitizePost([
            "id" => "int",
            "slug" => "string",
            "title" => "string",
            "content" => "string",
            "category_id" => "int",
            "user_id" => "int",
            "published" => "checkbox",
            "allow_comments" => "checkbox"
        ]);

        if (Validator::csrf("postcreate")) {
            if (Validator::post($post)) {
                $thePost = Post::create($post);

                if (is_object($thePost)) {
                    Messages::addSuccess("post.created");
                    Router::redirect("admin/posts/update/$thePost->id");
                } else {
                    Messages::addError("db.createpost");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        $data = [
            "action" => "create",
            "post" => $post
        ];
        $this->render("posts.update", "posts.createnewcategory", $data);
    }

    public function getUpdate(int $userId)
    {
        $thePost = Post::get($userId);
        if ($thePost === false) {
            Messages::addError("post.unknown");
            Router::redirect("admin/posts/read");
        }

        $data = [
            "action" => "update",
            "post" => $thePost->toArray()
        ];
        $this->render("posts.update", "posts.updatecategory", $data);
    }

    public function postUpdate()
    {
        $post = Validator::sanitizePost([
            "id" => "int",
            "slug" => "string",
            "title" => "string",
            "content" => "string",
            "category_id" => "int",
            "user_id" => "int",
            "published" => "checkbox",
            "allow_comments" => "checkbox"
        ]);

        if (Validator::csrf("postupdate")) {
            if (Validator::post($post)) {
                $thePost = Post::get($post["id"]);

                if (is_object($thePost)) {
                    if ($thePost->update($post)) {
                        Messages::addSuccess("post.updated");
                        Router::redirect("admin/posts/update/$thePost->id");
                    } else {
                        Messages::addError("db.postupdated");
                    }
                } else {
                    Messages::addError("post.unknown");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        $post["creation_datetime"] = Post::get($post["id"])->creation_datetime;

        $data = [
            "action" => "update",
            "post" => $post
        ];
        $this->render("posts.update", "posts.update", $data);
    }

    public function postDelete()
    {
        $id = (int)$_POST["id"];
        if (Validator::csrf("postdelete$id")) {
            $post = Post::get($id);
            if (is_object($post)) {
                if ($post->delete()) {
                    Messages::addSuccess("post.deleted");
                } else {
                    Messages::addError("post.deleting");
                }
            } else {
                Messages::addError("post.unknown");
            }
        } else {
            Messages::addError("csrffail");
        }

        Router::redirect("admin/posts/read");
    }
}
