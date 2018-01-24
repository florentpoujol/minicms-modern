<?php

namespace App\Controllers;

use App\Entities\Repositories\Comment as CommentRepo;
use App\Config;
use App\Form;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;

class Commentable extends BaseController
{
    /**
     * @var CommentRepo
     */
    public $commentRepo;

    /**
     * @var Form
     */
    public $form;

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config,
        CommentRepo $commentRepo, Form $formBuilder)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config);
        $this->commentRepo = $commentRepo;
        $this->form = $formBuilder;
    }

    protected function postComment(int $entityId): array
    {
        $strEntity = "post";
        if (static::class === "App\Controllers\Page") {
            $strEntity = "page";
        }
        $post = [
            "content" => (string)($_POST["content"] ?? ""),
            $strEntity . "_id" => $entityId,
            "user_id" => $this->user->id,
        ];

        if ($this->validator->csrf("comment_create_" . $this->user->id . "_$entityId")) {
            if ($this->validator->comment($post)) {
                $comment = $this->commentRepo->create($post);

                if (is_object($comment)) {
                    $this->session->addSuccess("comment.created");
                    $post["content"] = ""; // instead of redirecting, let fall back to the view
                } else {
                    $this->session->addError("comment.create");
                }
            }
        } else {
            $this->session->addError("csrffail");
        }

        return ["content" => $post["content"]];
    }

    public function render(string $view, array $data = [])
    {
        $data["form"] = $this->form;
        parent::render($view, $data);
    }
}
