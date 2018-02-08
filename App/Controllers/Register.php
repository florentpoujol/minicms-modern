<?php

namespace App\Controllers;

use App\Entities\Repositories\User as UserRepo;
use App\Config;
use App\Form;
use App\Lang;
use App\Mailer;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;

class Register extends BaseController
{
    /**
     * @var UserRepo
     */
    public $userRepo;

    /**
     * @var Mailer
     */
    public $mailer;

    /**
     * @var Form
     */
    public $form;

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config,
        UserRepo $userRepo, Mailer $mailer, Form $form)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config);
        $this->userRepo = $userRepo;
        $this->mailer = $mailer;
        $this->form = $form;
    }

    public function render(string $view, array $data = [])
    {
        $data["form"] = $this->form;
        if (!isset($data["post"])) {
            // post is the variable with which the form is populated
            $data["post"] = [];
        }
        parent::render($view, $data);
    }

    public function getRegister()
    {
        if ($this->redirectIfUserLoggedIn()) {
            return;
        }
        $this->render("register");
    }

    public function postRegister()
    {
        if ($this->redirectIfUserLoggedIn()) {
            return;
        }

        $post = $this->validator->sanitizePost([
            "register_name" => "string",
            "register_email" => "string",
            "register_password" => "string",
            "register_password_confirmation" => "string",
        ]);

        if ($this->validator->csrf("register")) {
            if ($this->validator->recaptcha()) {
                $user = [
                    "name" => $post["register_name"],
                    "email" => $post["register_email"],
                    "password" => $post["register_password"],
                    "password_confirmation" => $post["register_password_confirmation"],
                ];

                if ($this->validator->user($user)) {
                    unset($post["password_confirm"]);
                    $user = $this->userRepo->create($user);

                    if (is_object($user)) {
                        $this->session->addSuccess("user.created");

                        if ($this->mailer->sendConfirmEmail($user)) {
                            $this->session->addSuccess("email.confirmemail");
                            $this->router->redirect("login");
                            return;
                        } else {
                            $this->router->redirect("register/resendconfirmationemail");
                            return;
                        }
                    } else {
                        $this->session->addError("db.createuser");
                    }
                }
            } else {
                $this->session->addError("recaptchafail");
            }
        } else {
            $this->session->addError("csrffail");
        }

        $this->render("register", ["post" => $post]);
    }

    public function getConfirmEmail(int $userId, string $emailToken)
    {
        if ($this->redirectIfUserLoggedIn()) {
            return;
        }

        $user = $this->userRepo->get([
            "id" => $userId,
            "email_token" => $emailToken
        ]);

        if ($emailToken !== "" && $user !== false) {
            if ($user->updateEmailToken(""))  {
                $this->session->addSuccess("user.emailconfirmed");
                $this->router->redirect("login");
                return;
            } else {
                $this->session->addError("db.updateemailtoken");
            }
        } else {
            $this->session->addError("user.unauthorized");
            $this->router->redirect();
            return;
        }
    }

    public function getResendConfirmationEmail()
    {
        if ($this->redirectIfUserLoggedIn()) {
            return;
        }

        $this->render("resendconfirmationemail");
    }

    public function postResendConfirmationEmail()
    {
        if ($this->redirectIfUserLoggedIn()) {
            return;
        }

        $post = $this->validator->sanitizePost(["confirm_email" => "string"]);

        if ($this->validator->csrf("resendconfirmationemail")) {
            if ($this->validator->recaptcha()) {
                if ($this->validator->email($post["confirm_email"])) {
                    $user = $this->userRepo->get(["email" => $post["confirm_email"]]);

                    if (is_object($user)) {
                        if ($user->email_token !== "") {
                            if ($this->mailer->sendConfirmEmail($user)) {
                                $this->session->addSuccess("email.confirmemail");
                                $this->router->redirect("login");
                                return;
                            }
                        } else {
                            $this->session->addError("user.alreadyactivated");
                            $this->router->redirect("login");
                            return;
                        }
                    } else {
                        $this->session->addError("user.unknown");
                    }
                } else {
                    $this->session->addError("fieldvalidation.email");
                }
            } else {
                $this->session->addError("recaptchafail");
            }
        } else {
            $this->session->addError("csrffail");
        }

        $this->render("resendconfirmationemail", ["post" => $post]);
    }
}
