<?php

class RegisterController extends Controller
{

    function __construct()
    {
        parent::__construct();
        if (isLoggedIn() === true) {
            Messages::addError("You are already logged In");
            redirect();
        }
    }

    // --------------------------------------------------

    public function getIndex()
    {
        loadView("register", lang("register_title"), ["name" => "", "email" => ""]);
    }

    public function postIndex()
    {
        $newUser = [
            "name" => $_POST["register_name"],
            "email" => $_POST["register_email"],
            "password" => $_POST["register_password"],
            "password_confirm" => $_POST["register_password_confirm"]
        ];

        if (Validator::newUser($newUser) === true) {
            unset($newUser["password_confirm"]);
            $lastInsertId = Users::insert($newUser);

            if ($lastInsertId !== false) {
                Messages::addSuccess("new user created.");
                $user = Users::get(["id" => $lastInsertId]);

                if ($user !== false) {
                    if (Emails::sendConfirmEmail($user) === true) {
                        Messages::addSuccess("confirmation email sent");
                        redirect("login");
                    }
                    else {
                        Messages::addError("error sending the confirmation email");
                    }
                }
                else {
                    Messages::addError("error retrieving the new user. no email sent");
                }
            }
            else {
                Messages::addError("error registering new user");
            }
        }
        // error msgs already set in the Validator class

        loadView("register", lang("register_title"), ["name" => $newUser["name"], "email" => $newUser["email"]]);
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
            $success = Users::updateEmailToken($user->id);

            if ($success === true)  {
                Messages::addSuccess("email confirmed, please login.");
                redirect("login");
            }
            else {
                Messages::addError("error updating email token");
            }
        }
        else {
            Messages::addError("Can't accces that page.");
            redirect();
        }
    }

    public function getResendConfirmEmail()
    {
        loadView("resendconfirmemail", "Resend Confirmation Email", ["email" => ""]);
    }

    public function postResendConfirmEmail()
    {
        $email = $_POST["confirm_email"];

        if (Validator::email($email)) {
            $user = Users::get(["email" => $email]);

            if (is_object($user)) {
                if ($user->email_token !== "") {
                    if (Emails::sendConfirmEmail($user) === true) {
                        Messages::addSuccess("email sent");
                        redirect("login");
                    }
                    else {
                        Messages::addError("error sending email");
                    }
                }
                else {
                    Messages::addError("this email address is already confirmed. you can login with this user");
                    redirect("login");
                }
            }
            else {
                Messages::addError("no user with that email");
            }
        }
        else {
            Messages::addError("wrong email format");
        }

        loadView("resendconfirmemail", "Resend Confirmation Email", ["email" => $email]);
    }
}
