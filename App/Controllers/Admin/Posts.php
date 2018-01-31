<?php

namespace App\Controllers\Admin;

use App\Config;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;
use App\Entities\Repositories\Post as PostRepo;

class Posts extends AdminBaseController
{
    /**
     * @var PostRepo
     */
    public $postRepo;

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config,
        PostRepo $postRepo)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config);
        $this->postRepo = $postRepo;
    }

    public function getRead(int $pageNumber = 1)
    {
        $allRows = $this->postRepo->getAll(["pageNumber" => $pageNumber]);

        $data = [
            "allRows" => $allRows,
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => $this->postRepo->countAll(),
                "queryString" => $this->router->getQueryString("admin/posts/read/")
            ],
            "pageTitle" => $this->lang->get("posts.pagetitle"),
        ];
        $this->render("posts.read", $data);
    }

    public function getCreate()
    {
        $this->render("posts.update", [
            "action" => "create",
            "pageTitle" => $this->lang->get("posts.pagetitle"),
        ]);
    }

    public function postCreate()
    {
        $post = $this->validator->sanitizePost([
            "id" => "int",
            "slug" => "string",
            "title" => "string",
            "content" => "string",
            "category_id" => "int",
            "user_id" => "int",
            "published" => "checkbox",
            "allow_comments" => "checkbox",
        ]);

        if ($this->validator->csrf("postcreate")) {
            if ($this->validator->post($post)) {
                $thePost = $this->postRepo->create($post);

                if (is_object($thePost)) {
                    $this->session->addSuccess("post.created");
                    $this->router->redirect("admin/posts/update/$thePost->id");
                    return;
                } else {
                    $this->session->addError("db.createpost");
                }
            }
        } else {
            $this->session->addError("csrffail");
        }

        $data = [
            "action" => "create",
            "post" => $post,
            "pageTitle" => $this->lang->get("posts.createnew"),
        ];
        $this->render("posts.update", $data);
    }

    public function getUpdate(int $userId)
    {
        $thePost = $this->postRepo->get($userId);
        if ($thePost === false) {
            $this->session->addError("post.unknown");
            $this->router->redirect("admin/posts/read");
            return;
        }

        $data = [
            "action" => "update",
            "post" => $thePost->toArray(),
            "pageTitle" => $this->lang->get("posts.updatetitle"),
        ];
        $this->render("posts.update", $data);
    }

    public function postUpdate()
    {
        $post = $this->validator->sanitizePost([
            "id" => "int",
            "slug" => "string",
            "title" => "string",
            "content" => "string",
            "category_id" => "int",
            "user_id" => "int",
            "published" => "checkbox",
            "allow_comments" => "checkbox",
        ]);

        if ($this->validator->csrf("postupdate")) {
            if ($this->validator->post($post)) {
                $thePost = $this->postRepo->get($post["id"]);

                if (is_object($thePost)) {
                    if ($thePost->update($post)) {
                        $this->session->addSuccess("post.updated");
                        $this->router->redirect("admin/posts/update/$thePost->id");
                        return;
                    } else {
                        $this->session->addError("db.postupdated");
                    }
                } else {
                    $this->session->addError("post.unknown");
                }
            }
        } else {
            $this->session->addError("csrffail");
        }

        $post["creation_datetime"] = $this->postRepo->get($post["id"])->creation_datetime;

        $data = [
            "action" => "update",
            "post" => $post,
            "pageTitle" => $this->lang->get("posts.updatetitle"),
        ];
        $this->render("posts.update", $data);
    }

    public function postDelete()
    {
        $id = (int)$_POST["id"];
        if ($this->validator->csrf("postdelete$id")) {
            $post = $this->postRepo->get($id);
            if (is_object($post)) {
                if ($post->delete()) {
                    $this->session->addSuccess("post.deleted");
                } else {
                    $this->session->addError("post.deleting");
                }
            } else {
                $this->session->addError("post.unknown");
            }
        } else {
            $this->session->addError("csrffail");
        }

        $this->router->redirect("admin/posts/read");
    }
}
