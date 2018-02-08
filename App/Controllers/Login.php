<?php

namespace App\Controllers;

use App\Entities\Repositories\User as UserRepo;
use App\Config;
use App\Form;
use App\Helpers;
use App\Lang;
use App\Mailer;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;

class Login extends BaseController
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

    public function getLogin()
    {
        if ($this->redirectIfUserLoggedIn()) {
            return;
        }
        $this->render("login");
    }

    public function postLogin()
    {
        if ($this->redirectIfUserLoggedIn()) {
            return;
        }

        $post = $this->validator->sanitizePost([
            "login_name" => "string",
            "login_password" => "string"
        ]);

        if (!$this->validator->csrf("login")) {
            $this->session->addError("csrffail");
        } elseif (!$this->validator->recaptcha()) {
            $this->session->addError("recaptchafail");
        } else {
            $formatOK = true;

            if (!$this->validator->name($post["login_name"])) {
                $formatOK = false;
                $this->session->addError("fieldvalidation.name");
            }

            if (!$this->validator->password($post["login_password"])) {
                $formatOK = false;
                $this->session->addError("fieldvalidation.password");
            }

            if ($formatOK) {
                $user = $this->userRepo->get(["name" => $post["login_name"]]);

                if (!is_object($user)) {
                    $this->session->addError("user.unknown");
                }
                elseif ($user->email_token !== "") {
                    $this->session->addError("user.notactivated");
                    $this->router->redirect("register/resendconfirmationemail");
                    return;
                }
                elseif (!password_verify($post["login_password"], $user->password_hash)) {
                    $this->session->addError("user.wrongpassword");
                }
                else {
                    $this->session->set("minicms_modern_auth", $user->id);
                    $this->session->addSuccess("user.loggedin", ["username" => $user->name]);
                    $this->router->redirect("admin");
                    return;
                }
            }
        }

        $this->render("login", ["post" => $post]);
    }

    public function getLostpassword()
    {
        if ($this->redirectIfUserLoggedIn()) {
            return;
        }

        $this->render("lostpassword");
    }

    public function postLostpassword()
    {
        if ($this->redirectIfUserLoggedIn()) {
            return;
        }

        $post = $this->validator->sanitizePost([
            "lostpassword_email" => "string"
        ]);

        $email = $post["lostpassword_email"];

        if ($this->validator->csrf("lostpassword")) {
            if ($this->validator->email($email)) {
                $user = $this->userRepo->get(["email" => $email]);

                if (is_object($user)) {
                    $token = (new Helpers())->getUniqueToken();

                    if ($user->updatePasswordToken($token)) {
                        if ($this->mailer->sendChangePassword($user)) {
                            $this->session->addSuccess("email.changepassword");
                        }
                    } else {
                        $this->session->addError("error.error");
                    }
                } else {
                    $this->session->addError("user.unknown");
                }
            } else {
                $this->session->addError("fieldvalidation.email");
            }
        } else {
            $this->session->addError("csrffail");
        }

        $this->render("lostpassword", ["post" => $post]);
    }

    public function getResetPassword(int $userId, string $passwordToken)
    {
        if ($this->redirectIfUserLoggedIn()) {
            return;
        }

        $user = $this->userRepo->get([
            "id" => $userId,
            "password_token" => $passwordToken
        ]);

        if (
            $passwordToken !== "" && $user !== false &&
            time() < $user->password_change_time + (3600 * 48)
        ) {
            $this->render("resetpassword", ["userName" => $user->name]);
        } else {
            $this->session->addError("user.unauthorized");
            $this->router->redirect("login/lostpassword");
        }
    }

    public function postResetPassword(int $userId, string $passwordToken)
    {
        if ($this->redirectIfUserLoggedIn()) {
            return;
        }

        $user = $this->userRepo->get([
            "id" => $userId,
            "password_token" => $passwordToken
        ]);

        $post = $this->validator->sanitizePost([
            "resetpassword" => "string",
            "resetpassword_confirm" => "string"
        ]);

        if (
            $this->validator->csrf("resetpassword") &&
            $passwordToken !== "" && $user !== false &&
            time() < $user->password_change_time + (3600 * 48)
        ) {
            if ($this->validator->password($post["resetpassword"], $post["resetpassword_confirm"])) {
                if ($user->updatePassword($post["resetpassword"])) {
                    $this->session->addSuccess("passwordchanged");
                    $this->router->redirect("login");
                    return;
                } else {
                    $this->session->addError("db.resetpassword");
                }
            } else {
                $this->session->addError("fieldvalidation.passwordnotequal");
            }

            $this->render("resetpassword", ["userName" => $user->name]);
        } else {
            $this->session->addError("unauthorized");
            $this->router->redirect("login/lostpassword");
        }
    }
}
