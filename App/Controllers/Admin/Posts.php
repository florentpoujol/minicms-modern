<?php

namespace App\Controllers\Admin;

use App\Entities\Post;
use App\Messages;
use App\Route;
use App\Validate;

class Posts extends AdminBaseController
{
    public function __construct($user)
    {
        parent::__construct($user);

        if ($this->user->isCommenter()) {
            Route::redirect("admin/posts/update/".$this->user->id);
        }
    }

    public function getRead($pageNumber = 1)
    {
        $allRows = Post::getAll(["pageNumber" => $pageNumber]);

        $data = [
            "allRows" => $allRows,
            "pageNumber" => $pageNumber,
            "pagination" => [
                "itemsCount" => Post::countAll(),
                "queryString" => Route::buildQueryString("admin/posts/read/")
            ]
        ];
        $this->render("posts.read", "posts.pagetitle", $data);
    }

    public function getCreate()
    {
        $this->render("posts.update", "posts.createnewcategory", ["action" => "create"]);
    }

    public function postCreate()
    {
        $post = [];
        if (Validate::csrf("postcreate")) {
            $post = Validate::sanitizePost([
                "id" => "int",
                "slug" => "string",
                "title" => "string",
                "content" => "string",
                "category_id" => "int",
                "published" => "int",
                "allow_comments" => "int"
            ]);

            if (Validate::post($post)) {
                $thePost = Post::create($post);

                if (is_object($thePost)) {
                    Messages::addSuccess("post.created");
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

    public function getUpdate($id)
    {
        $thePost = Post::get($id);
        if ($thePost === false) {
            Messages::addError("post.unknown");
            Route::redirect("admin/posts/read");
        }

        $data = [
            "action" => "update",
            "post" => $thePost->toArray()
        ];
        $this->render("posts.update", "posts.updatecategory", $data);
    }

    public function postUpdate()
    {
        $post = [];
        if (Validate::csrf("categoryupdate")) {
            $post = Validate::sanitizePost([
                "id" => "int",
                "slug" => "string",
                "title" => "string",
                "content" => "string",
                "category_id" => "int",
                "published" => "int",
                "allow_comments" => "int"
            ]);

            if (Validate::post($post)) {
                $thePost = Post::get($post["id"]);

                if (is_object($thePost)) {
                    if ($thePost->update($post)) {
                        Messages::addSuccess("post.updated");
                        Route::redirect("admin/posts/update/".$post["id"]);
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

        $data = [
            "action" => "update",
            "post" => $post
        ];
        $this->render("posts.update", "posts.createnewpost", $data);
    }

    public function postDelete()
    {
        $id = (int)$_POST["post_id"];
        if (Validate::csrf("postdelete$id")) {
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

        Route::redirect("admin/posts/read");
    }
}
