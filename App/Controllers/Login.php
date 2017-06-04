<?php

namespace App\Controllers;

use App\Messages;
use App\Validate;
use App\Route;
use App\Models\Users;

class Login extends BaseController
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
        $this->render("login");
    }

    public function postIndex()
    {
        $post = Validate::sanitizePost([
            "login_name" => "string",
            "login_password" => "string",
            "login_csrf_token" => "string"
        ]);

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
                $dbUser = Users::get(["name" => $post["login_name"]]);

                if (is_object($dbUser)) {
                    if ($dbUser->email_token === "") {
                        if (password_verify($post["login_password"], $dbUser->password_hash)) {

                            $this->user = new \App\Entities\User($dbUser);

                            \App\Session::set("minicms_mvc_auth", $this->user->id);
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


    public function getResetPassword($userId, $passwordToken)
    {
        $user = Users::get([
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

    public function postResetPassword($userId, $passwordToken)
    {
        $user = Users::get([
            "id" => $userId,
            "password_token" => $passwordToken
        ]);

        $post = Validate::sanitizePost([
            "resetpassword" => "string",
            "resetpassword_confirm" => "string"
        ]);

        if (
            $passwordToken !== "" && $user !== false &&
            time() < $user->password_change_time + (3600 * 48) &&
            Validate::csrf("resetpassword")
        ) {
            if (Validate::password($post["resetpassword"], $post["resetpassword_confirm"])) {
                $success = Users::updatePassword($user->id, $post["resetpassword"]);

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
