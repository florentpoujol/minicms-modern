<?php

namespace App\Controllers;


use App\Entities\Repositories\Comment as CommentRepo;
use App\Entities\Repositories\Post as PostRepo;
use App\Config;
use App\Form;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;

class Post extends Commentable
{
    /**
     * @var PostRepo
     */
    public $postRepo;

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config, CommentRepo $commentRepo,
        PostRepo $postRepo, Form $form)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config, $commentRepo, $form);
        $this->postRepo = $postRepo;
    }

    public function getPost(int $postId)
    {
        $post = $this->postRepo->get($postId);
        if ($post === false) {
            $this->session->addError("post.unknown");
            $this->router->redirect("blog");
            return;
        }

        $data = [
            "post" => $post,
            "pageTitle" => $post->title,
        ];
        $this->render("post", $data);
    }

    public function postPost(int $postId)
    {
        if (! isset($this->user)) {
            $this->session->addError("user.mustbeloggedintopostcomment");
            $this->router->redirect("post/$postId");
            return;
        }

        $post = $this->postRepo->get($postId);
        if ($post === false) {
            $this->session->addError("post.unknown");
            $this->router->redirect("blog");
            return;
        }

        $data = [
            "post" => $post,
            "pageTitle" => $post->title,
            "commentPost" => $this->postComment($postId)
        ];
        $this->render("post", $data);
    }
}
