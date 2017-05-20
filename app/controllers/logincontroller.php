<?php

namespace App\Controllers;

use App\Messages;
use App\Validate;
use App\Models\Users;

class LoginController extends Controller
{

    function __construct()
    {
        parent::__construct();
        if (isLoggedIn() === true) {
            Messages::addError("user.alreadyloggedin");
            redirect();
        }
    }

    // --------------------------------------------------

    public function getIndex()
    {
        $this->render("login", "login.title");
    }

    public function postIndex()
    {
        $schema = [
            "login_name" => "string",
            "login_password" => "string",
            "login_csrf_token" => "string"
        ];
        $post = Validate::sanitizePost($schema);

        if (Validate::csrf("login")) {
            $formatOK = true;

            if(! Validate::name($post["login_name"])) {
                $formatOK = false;
                Messages::addError("fieldvalidation.name");
            }

            if(! Validate::password($post["login_password"])) {
                $formatOK = false;
                Messages::addError("fieldvalidation.password");
            }

            if ($formatOK) {
                $_user = Users::get(["name" => $post["login_name"]]);

                if (is_object($_user)) {
                    if ($_user->email_token === "") {
                        if (password_verify($password, $_user->password_hash)) {
                            global $user;
                            $user = $_user;
                            $this->user = $user;
                            Session::set("minicms_mvc_auth", $this->user->id);
                            Messages::addSuccess("loggedin");
                            redirect("admin");
                        }
                        else {
                            Messages::addError("loginwrongpassword");
                        }
                    }
                    else {
                        Messages::addError("usernotactivated");
                        redirect("register", "resendconfirmemail");
                    }
                }
                else {
                    Messages::addError("unknowuser");
                }
            }
        }
        else {
            Messages::addError("csrffail");
        }

        $this->render("login", "login.title", ["post" => $post]);
    }

    // --------------------------------------------------

    public function getLostPassword()
    {
        $this->render("lostpassword");
    }

    public function postLostPassword()
    {
        $post = Validate::sanitizePost([
            "lostpassword_email" => "string",
            "lostpassword_csrf_token" => "string"
        ]);

        $email = $post["lostpassword_email"];

        if (Validate::csrf("lostpassword")) {
            if (Validate::email($email)) {
                $user = Users::get(["email" => $email]);

                if (is_object($user)) {
                    $token = \App\Security::getUniqueToken();
                    $success = Users::updatePasswordToken($user->id, $token);

                    if ($success) {
                        $user->password_token = $token;
                        \App\Emails::sendChangePassword($user);
                        Messages::addSuccess("email.changepassword");
                    }
                }
                else {
                    Messages::addError("unknowuser");
                }
            }
            else {
                Messages::addError("fieldvalidation.email");
            }
        }
        else {
            Messages::addError("csrffail");
        }

        $this->render("lostpassword", null, ["post" => $post]);
    }

    // --------------------------------------------------

    public function getResetPassword()
    {
        $token = trim($_GET["token"]);
        $user = Users::get([
            "id" => $_GET["id"],
            "password_token" => $token
        ]);

        if (
            $token !== "" && $user !== false &&
            time() < $user->password_change_time + (3600 * 48)
        ) {
            $this->render("resetpassword", null, ["userName" => $user->name]);
        }
        else {
            Messages::addError("unauthorized");
            redirect();
        }
    }

    public function postResetPassword()
    {
        $token = trim($_GET["token"]);
        $user = Users::get([
            "id" => $_GET["id"],
            "password_token" => $token
        ]);

        $post = Validate::sanitizePost([
            "resetpassword_" => "string",
            "resetpassword_confirm" => "string"
        ]);

        if (
            $token !== "" && $user !== false &&
            time() < $user->password_change_time + (3600 * 48) &&
            Validate::csrf("resetpassword")
        ) {
            if (Validate::password($post["resetpassword"], $post["resetpassword_confirm"])) {
                $success = Users::updatePassword($user->id, $post["resetpassword"]);

                if ($success) {
                    Messages::addSuccess("passwordchanged");
                    redirect("login");
                }
                else {
                    Messages::addError("db.resetpassword");
                }
            }
            else {
                Messages::addError("fieldvalidation.passwordformatornotequal");
            }

            $this->render("resetpassword", null, ["userName" => $user->name]);
        }
        else {
            Messages::addError("unauthorized");
            redirect();
        }
    }
}
