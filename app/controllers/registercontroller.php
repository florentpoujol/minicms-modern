<?php

namespace App\Controllers;

use App\Messages;
use App\Validate;
use App\Models\Users;
use App\Emails;

class RegisterController extends Controller
{

    function __construct()
    {
        parent::__construct();
        if (isset($this->user)) {
            Messages::addError("user.alreadyloggedin");
            redirect();
        }
    }

    // --------------------------------------------------

    public function getIndex()
    {
        $this->render("register", null, ["post" => []]);
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
                $lastInsertId = Users::insert($post);

                if (is_int($lastInsertId)) {
                    Messages::addSuccess("user.created");
                    $user = Users::get(["id" => $lastInsertId]);

                    if (is_object($user)) {
                        if (Emails::sendConfirmEmail($user)) {
                            Messages::addSuccess("email.confirmemail");
                            redirect("login");
                        }
                    } else {
                        Messages::addError("error");
                    }
                } else {
                    Messages::addError("db.createuser");
                }
            }
        }

        $this->render("register", null, ["post" => $post]);
    }

    // --------------------------------------------------

    public function getConfirmEmail()
    {
        $token = trim($_GET["token"]);
        $user = Users::get([
            "id" => $_GET["id"],
            "email_token" => $token
        ]);

        if ($token !== "" && $user !== false) {
            if (Users::updateEmailToken($user->id))  {
                Messages::addSuccess("user.emailconfirmed");
                redirect("login");
            } else {
                Messages::addError("db.updateemailtoken");
            }
        } else {
            Messages::addError("user.unauthorized");
            redirect();
        }
    }

    public function getResendConfirmationEmail()
    {
        $this->render("resendconfirmemail", null, ["post" => []]);
    }

    public function postResendConfirmationEmail()
    {
        $post = Validate::sanitizePost(["confirm_email" => "string"]);

        if (Validate::csrf("resendconfirmemail")) {
            if (Validate::email($post["confirm_email"])) {
                $user = Users::get(["email" => $post["confirm_email"]]);

                if (is_object($user)) {
                    if ($user->email_token !== "") {
                        if (Emails::sendConfirmEmail($user)) {
                            Messages::addSuccess("email.confirmemail");
                            redirect("login");
                        }
                    } else {
                        Messages::addError("user.alreadyactivated");
                        redirect("login");
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
