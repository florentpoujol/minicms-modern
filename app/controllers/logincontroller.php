<?php

class LoginController extends Controller
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
        loadView("login", lang("login_title"));
    }

    public function postIndex()
    {
        $loginName = $_POST["login_name"];
        $password = $_POST["login_password"];
        // $recaptcha_response = $_POST["g-recaptcha-response"];

        if (strlen($loginName) === 0 || strlen($password) === 0) {
            Messages::addError("The name or password is empty !");
        }

        // elseif (verifyRecaptcha($recaptcha_response) === true) {
        else {
            $_user = Users::get(["name" => $loginName]);

            if (is_object($_user)) {
                if ($_user->email_token === "") {
                    if (password_verify($password, $_user->password_hash) === true) {
                        global $user;
                        $user = $_user;
                        $this->user = $user;
                        $_SESSION["minicms_mvc_auth"] = $this->user->id;
                        Messages::addSuccess("you are logged in !");
                        redirect("admin");
                    }
                    else {
                        Messages::addError("Wrong password !");
                    }
                }
                else {
                    Messages::addError("This user is not activated yet. You need to click the link in the email that has been sent just after registration. You can send this email again from this page.");
                    redirect("register", "resendconfirmemail");
                }
            }
            else {
                Messages::addError("No user by that name !");
            }
        }

        loadView("login", lang("login_title"));
    }

    // --------------------------------------------------

    public function getLostPassword()
    {
        loadView("lostpassword", lang("lostpassword"));
    }

    public function postLostPassword()
    {
        $email = $_POST["forgot_password_email"];

        if (Validator::email($email) === true) {
            $user = Users::get(["email" => $email]);

            if ($user !== false) {
                $token = md5(microtime(true)+mt_rand());
                $success = Users::updatePasswordToken($user->id, $token);

                if ($success === true) {
                    $user->password_token = $token;
                    Emails::sendChangePassword($user);
                    Messages::addSuccess("An email has been sent to this address. Click the link within 48 hours.");
                }
            }
            else {
                Messages::addError("No users has that email.");
            }
        }
        else {
            Messages::addError("Wrong email format.");
        }

        loadView("lostpassword", lang("lostpassword"));
    }

    // --------------------------------------------------

    public function getResetPassword()
    {
        $token = trim($_GET["token"]);
        $user = Users::get([
            "id" => $_GET["id"],
            "password_token" => $token
        ]);

        if ($token !== "" && $user !== false &&
            time() < $user->password_change_time + (3600 * 48)) {
            loadView("resetpassword", "Reset your password", ["userName" => $user->name]);
        }
        else {
            Messages::addError("Can't accces that page.");
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

        if ($token !== "" && $user !== false &&
            time() < $user->password_change_time + (3600 * 48)) {
            $password = $_POST["reset_password"];
            $passwordOK = Validator::password($password, $_POST["reset_password_confirm"]);

            if ($passwordOK === true) {
                $success = Users::updatePassword($user->id, $password);

                if ($success === true) {
                    Messages::addSuccess("password changed successfully");
                    redirect("login");
                }
                else {
                    Messages::addError("error changing password");
                }
            }

            loadView("resetpassword", "Reset your password");
        }
        else {
            Messages::addError("Can't accces that page.");
            redirect();
        }
    }
}
