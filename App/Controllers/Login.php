<?php

namespace App\Controllers;

use App\Messages;
use App\Validator;
use App\Route;
use App\Entities\User;

class Login extends BaseController
{
    function __construct(User $user)
    {
        parent::__construct($user);

        if (isset($this->user)) {
            Messages::addError("user.alreadyloggedin");
            Route::redirect("admin");
        }
    }

    public function getLogin()
    {
        $this->render("login");
    }

    public function postLogin()
    {
        $post = Validator::sanitizePost([
            "login_name" => "string",
            "login_password" => "string"
        ]);

        if (Validator::csrf("login")) {
            $formatOK = true;

            if(! Validator::name($post["login_name"])) {
                $formatOK = false;
                Messages::addError("fieldvalidation.name");
            }

            if(! Validator::password($post["login_password"])) {
                $formatOK = false;
                Messages::addError("fieldvalidation.password");
            }

            if ($formatOK) {
                $user = User::get(["name" => $post["login_name"]]);

                if (is_object($user)) {
                    if ($user->email_token === "") {
                        if (password_verify($post["login_password"], $user->password_hash)) {

                            $this->user = $user;

                            \App\Session::set("minicms_modern_auth", $this->user->id);
                            Messages::addSuccess("user.loggedin", ["username" => $this->user->name]);

                            Route::redirect("admin");

                        } else {
                            Messages::addError("user.wrongpassword");
                        }
                    } else {
                        Messages::addError("user.notactivated");
                        Route::redirect("register/resendconfirmationemail");
                    }
                } else {
                    Messages::addError("user.unknow");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        $this->render("login", null, ["post" => $post]);
    }

    public function getLostPassword()
    {
        $this->render("lostpassword");
    }

    public function postLostPassword()
    {
        $post = Validator::sanitizePost([
            "lostpassword_email" => "string"
        ]);

        $email = $post["lostpassword_email"];

        if (Validator::csrf("lostpassword")) {
            if (Validator::email($email)) {
                $user = User::get(["email" => $email]);

                if (is_object($user)) {
                    $token = \App\Security::getUniqueToken();
                    $success = $user->updatePasswordToken($token);

                    if ($success) {
                        \App\Emails::sendChangePassword($user);
                        Messages::addSuccess("email.changepassword");
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

        $this->render("lostpassword", null, ["post" => $post]);
    }

    public function getResetPassword(int $userId, string $passwordToken)
    {
        $user = User::get([
            "id" => $userId,
            "password_token" => $passwordToken
        ]);

        if (
            $passwordToken !== "" && $user !== false &&
            time() < $user->password_change_time + (3600 * 48)
        ) {
            $this->render("resetpassword", null, ["userName" => $user->name]);
        } else {
            Messages::addError("user.unauthorized");
            Route::redirect(); // todo: properly redirect to 301 page, same for places
        }
    }

    public function postResetPassword(int $userId, string $passwordToken)
    {
        $user = User::get([
            "id" => $userId,
            "password_token" => $passwordToken
        ]);

        $post = Validator::sanitizePost([
            "resetpassword" => "string",
            "resetpassword_confirm" => "string"
        ]);

        if (
            $passwordToken !== "" && $user !== false &&
            time() < $user->password_change_time + (3600 * 48) &&
            Validator::csrf("resetpassword")
        ) {
            if (Validator::password($post["resetpassword"], $post["resetpassword_confirm"])) {
                $success = $user->updatePassword($post["resetpassword"]);

                if ($success) {
                    Messages::addSuccess("passwordchanged");
                    Route::redirect("login");
                } else {
                    Messages::addError("db.resetpassword");
                }
            } else {
                Messages::addError("fieldvalidation.passwordnotequal");
            }

            $this->render("resetpassword", null, ["userName" => $user->name]);
        } else {
            Messages::addError("unauthorized");
            Route::redirect();
        }
    }
}
