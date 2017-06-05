<?php

namespace App\Controllers;

use App\Messages;
use App\Validate;
use App\Route;
use App\Emails;
use App\Entities\User;

class Register extends BaseController
{

    function __construct($user)
    {
        parent::__construct($user);

        if (isset($this->user)) {
            Messages::addError("user.alreadyloggedin");
            Route::redirect("admin");
        }
    }

    public function getIndex($idOrSlug = null)
    {
        $this->render("register");
    }

    public function postIndex()
    {
        $post = [];

        if (Validate::csrf("register")) {
            $post = Validate::sanitizePost([
                "register_name"             => "string",
                "register_email"            => "string",
                "register_password"         => "string",
                "register_password_confirm" => "string"
            ]);

            $user = [
                "name" => $post["register_name"],
                "email" => $post["register_email"],
                "password" => $post["register_password"],
                "password_confirm" => $post["register_password_confirm"]
            ];

            if (Validate::user($user)) {
                unset($post["password_confirm"]);
                $user = User::insert($post);

                if (is_object($user)) {
                    Messages::addSuccess("user.created");

                    if (Emails::sendConfirmEmail($user)) {
                        Messages::addSuccess("email.confirmemail");
                        Route::redirect("login");
                    }
                } else {
                    Messages::addError("db.createuser");
                }
            }
        }

        $this->render("register", null, ["post" => $post]);
    }

    public function getConfirmEmail($userId, $emailToken)
    {
        $user = User::get([
            "id" => $userId,
            "email_token" => $emailToken
        ]);

        if ($emailToken !== "" && $user !== false) {
            if ($user->updateEmailToken(""))  {
                Messages::addSuccess("user.emailconfirmed");
                Route::redirect("login");
            } else {
                Messages::addError("db.updateemailtoken");
            }
        } else {
            Messages::addError("user.unauthorized");
            Route::redirect();
        }
    }

    public function getResendConfirmationEmail()
    {
        $this->render("resendconfirmemail");
    }

    public function postResendConfirmationEmail()
    {
        $post = Validate::sanitizePost(["confirm_email" => "string"]);

        if (Validate::csrf("resendconfirmemail")) {
            if (Validate::email($post["confirm_email"])) {
                $user = User::get(["email" => $post["confirm_email"]]);

                if (is_object($user)) {
                    if ($user->email_token !== "") {
                        if (Emails::sendConfirmEmail($user)) {
                            Messages::addSuccess("email.confirmemail");
                            Route::redirect("login");
                        }
                    } else {
                        Messages::addError("user.alreadyactivated");
                        Route::redirect("login");
                    }
                } else {
                    Messages::addError("user.unknow");
                }
            } else {
                Messages::addError("fieldvalidation.email");
            }
        } else {
            Messages::addError("csrffail");
        }

        $this->render("resendconfirmemail", null, $post);
    }
}