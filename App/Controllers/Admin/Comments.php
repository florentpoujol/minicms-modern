<?php

namespace App\Controllers\Admin;

use App\Config;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;
use App\Entities\Repositories\Comment as CommentRepo;

class Comments extends AdminBaseController
{
    /**
     * @var CommentRepo
     */
    public $commentRepo;

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config,
        CommentRepo $commentRepo)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config);
        $this->commentRepo = $commentRepo;
    }

    public function getRead(int $pageNumber = 1)
    {
        $params = ["pageNumber" => $pageNumber];
        $allCommentsForThatUser = [];
        $totalCommentCount = 0;
        $userId = $this->user->id;

        if ($this->user->isAdmin()) {
            $allCommentsForThatUser = $this->commentRepo->getAll($params);
            $totalCommentCount = $this->commentRepo->countAll();
        } elseif ($this->user->isWriter()) {
            $allCommentsForThatUser = $this->commentRepo->getAllForEditor($this->user, $pageNumber);
            $totalCommentCount = count($this->commentRepo->getAllForEditor($this->user));
        } else {
            $params["user_id"] = $userId;
            $allCommentsForThatUser = $this->commentRepo->getAll($params);
            $totalCommentCount = $this->commentRepo->countAll(["user_id" => $userId]);
        }

        $data = [
            "allRows" => $allCommentsForThatUser,
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => $totalCommentCount,
                "queryString" => $this->router->getQueryString("admin/comments/read")
            ],
            "pageTitle" => $this->lang->get("admin.comment.readtitle"),
        ];
        $this->render("comments.read", $data);
    }

    public function getUpdate(int $commentId)
    {
        $comment = $this->commentRepo->get($commentId);
        if ($comment === false) {
            $this->session->addError("comment.unknown");
            $this->router->redirect("admin/comments/read");
            return;
        }

        $data = [
            "action" => "update",
            "post" => $comment->toArray(),
            "pageTitle" => $this->lang->get("admin.comment.updatetitle"),
        ];
        $this->render("comments.update", $data);
    }

    public function postUpdate()
    {
        $post = $this->validator->sanitizePost([
            "id" => "int",
            "content" => "string",
            "user_id" => "int",
        ]);

        if ($this->validator->csrf("commentupdate")) {
            if ($this->validator->comment($post)) {
                $comment = $this->commentRepo->get($post["id"]);

                if (is_object($comment)) {
                    if ($comment->update($post)) {
                        $this->session->addSuccess("comment.updated");
                        $this->router->redirect("admin/comments/update/$comment->id");
                        return;
                    } else {
                        $this->session->addError("db.commentupdated");
                    }
                } else {
                    $this->session->addError("comment.unknown");
                }
            }
        } else {
            $this->session->addError("csrffail");
        }

        $post["creation_datetime"] = $this->commentRepo->get($post["id"])->creation_datetime;

        $data = [
            "action" => "update",
            "post" => $post,
            "pageTitle" => $this->lang->get("admin.page.updatetitle"),
        ];
        $this->render("pages.update", $data);
    }

    public function postDelete()
    {
        $id = (int)$_POST["id"];
        if ($this->validator->csrf("commentdelete$id")) {
            $comment = $this->commentRepo->get($id);
            if (is_object($comment)) {
                if ($comment->delete()) {
                    $this->session->addSuccess("comment.deleted");
                } else {
                    $this->session->addError("comment.deleting");
                }
            } else {
                $this->session->addError("comment.unknown");
            }
        } else {
            $this->session->addError("csrffail");
        }

        $this->router->redirect("admin/comments/read");
    }
}
