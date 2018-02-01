<?php

namespace App\Controllers\Admin;

use App\Config;
use App\Form;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;
use App\Entities\Repositories\Post as PostRepo;
use App\Entities\Repositories\Category as CategoryRepo;
use App\Entities\Repositories\User as UserRepo;

class Posts extends AdminBaseController
{
    /**
     * @var PostRepo
     */
    protected $postRepo;

    /**
     * @var CategoryRepo
     */
    protected $categoryRepo;

    /**
     * @var UserRepo
     */
    protected $userRepo;

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config, Form $form,
        PostRepo $postRepo, CategoryRepo $categoryRepo, UserRepo $userRepo)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config, $form);
        $this->postRepo = $postRepo;
        $this->categoryRepo = $categoryRepo;
        $this->userRepo = $userRepo;
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
        ];
        $this->render("posts.read", $data);
    }

    public function getCreate()
    {
        $this->render("posts.update", [
            "action" => "create",
            "categories" => $this->categoryRepo->getAll(),
            "users" => array_merge(
                $this->userRepo->getAll(["role" => "admin"]),
                $this->userRepo->getAll(["role" => "writer"])
            ),
        ]);
    }

    public function postCreate()
    {
        $post = $this->validator->sanitizePost([
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
            "categories" => $this->categoryRepo->getAll(),
            "users" => array_merge(
                $this->userRepo->getAll(["role" => "admin"]),
                $this->userRepo->getAll(["role" => "writer"])
            ),
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
            "categories" => $this->categoryRepo->getAll(),
            "users" => array_merge(
                $this->userRepo->getAll(["role" => "admin"]),
                $this->userRepo->getAll(["role" => "writer"])
            ),
        ];
        $this->render("posts.update", $data);
    }

    public function postUpdate(int $postId)
    {
        $post = $this->validator->sanitizePost([
            "slug" => "string",
            "title" => "string",
            "content" => "string",
            "category_id" => "int",
            "user_id" => "int",
            "published" => "checkbox",
            "allow_comments" => "checkbox",
        ]);
        $post["id"] = $postId;

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
            "categories" => $this->categoryRepo->getAll(),
            "users" => array_merge(
                $this->userRepo->getAll(["role" => "admin"]),
                $this->userRepo->getAll(["role" => "writer"])
            ),
        ];
        $this->render("posts.update", $data);
    }

    public function postDelete(int $postId)
    {
        if ($this->validator->csrf("postdelete$postId")) {
            $post = $this->postRepo->get($postId);
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
