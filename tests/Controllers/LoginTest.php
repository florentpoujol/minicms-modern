<?php

namespace Tests\Controllers;

use App\Controllers\Login;
use Tests\DatabaseTestCase;

class LoginTest extends DatabaseTestCase
{
    function testGetLogin()
    {
        $controller = $this->container->make(Login::class);

        $content = $this->getControllerOutput($controller, "getLogin");

        $this->assertContains('<h1>' . $this->lang->get("login.pagetitle") . '</h1>', $content);
        $this->assertContains('<form action="' . $this->router->getQueryString("login") . '"', $content);
        $this->assertContains('<a href="' . $this->router->getQueryString("login/lostpassword") . '">Forgot Password ?</a>', $content);

        // logged in user
        $controller->setLoggedInUser($this->userRepo->get(1));
        $content = $this->getControllerOutput($controller, "getLogin");

        $this->assertEmpty(trim($content));
        $this->assertContains("user.alreadyloggedin", $this->session->getErrors());
        $this->assertRedirectTo("admin");
    }

    function testPostLoginFail()
    {
        $controller = $this->container->make(Login::class);

        // wrong CSRF
        $content = $this->getControllerOutput($controller, "postLogin");
        $this->assertContains("csrffail", $content);

        // wrong fields format
        $_POST["login_name"] = "a";
        $_POST["login_password"] = "a";
        $this->setupCSRFToken("login");

        $content = $this->getControllerOutput($controller, "postLogin");
        $this->assertContains("login.pagetitle", $content);
        $this->assertContains("fieldvalidation.name", $content);
        $this->assertContains("fieldvalidation.password", $content);

        // Unknown username
        $_POST["login_name"] = "JohnDoe";
        $_POST["login_password"] = "Az1";
        $this->setupCSRFToken("login");

        $content = $this->getControllerOutput($controller, "postLogin");
        $this->assertContains("login.pagetitle", $content);
        $this->assertContains("user.unknown", $content);

        // user not activated
        $user = $this->userRepo->get(1);
        $user->updateEmailToken("thetoken");
        $_POST["login_name"] = $user->name;
        $this->setupCSRFToken("login");

        $this->getControllerOutput($controller, "postLogin");
        $errors = $this->session->getErrors();
        $this->assertContains("user.notactivated", $errors);
        $this->assertRedirectTo("register/resendconfirmationemail");

        // wrong password
        $user->updateEmailToken("");
        $_POST["login_name"] = $user->name;
        $_POST["login_password"] = "Az1"; // password is currently ""
        $this->setupCSRFToken("login");

        $content = $this->getControllerOutput($controller, "postLogin");
        $this->assertContains("user.wrongpassword", $content);
    }

    function testPostLoginSuccess()
    {
        $user = $this->userRepo->get(1);
        $user->updatePassword("Az1");
        $_POST["login_name"] = $user->name;
        $_POST["login_password"] = "Az1";
        $this->setupCSRFToken("login");

        $this->session->delete("minicms_modern_auth");
        $this->assertSame(null, $this->session->get("minicms_modern_auth"));

        $controller = $this->container->make(Login::class);
        $this->getControllerOutput($controller, "postLogin");
        $this->assertSame($user->id, $this->session->get("minicms_modern_auth"));
        $this->assertContains("Welcome $user->name, you are now logged in", $this->session->getSuccesses());
    }

    function testGetLostPassword()
    {
        $controller = $this->container->make(Login::class);
        $content = $this->getControllerOutput($controller, "getLostpassword");

        $this->assertContains('<h1>' . $this->lang->get("lostpassword.pagetitle") . '</h1>', $content);
        $this->assertContains('<form action="' . $this->router->getQueryString("login/lostpassword") . '"', $content);
        $this->assertContains('"Request password change"', $content);

        // use logged in
        $controller->setLoggedInUser($this->userRepo->get(1));
        $content = $this->getControllerOutput($controller, "getLostpassword");
        $this->assertEmpty(trim($content));
        $this->assertContains("user.alreadyloggedin", $this->session->getErrors());
        $this->assertRedirectTo("admin");
    }

    function testPostLostPasswordFail()
    {
        $controller = $this->container->make(Login::class);

        // wrong CSRF
        $content = $this->getControllerOutput($controller, "postLostpassword");
        $this->assertContains("csrffail", $content);

        // wrong fields format
        $_POST["lostpassword_email"] = "a";
        $this->setupCSRFToken("lostpassword");

        $content = $this->getControllerOutput($controller, "postLostpassword");
        $this->assertContains("lostpassword.pagetitle", $content);
        $this->assertContains("fieldvalidation.email", $content);

        // Unknown email
        $_POST["lostpassword_email"] = "email@email.fr";
        $this->setupCSRFToken("lostpassword");

        $content = $this->getControllerOutput($controller, "postLostpassword");
        $this->assertContains("lostpassword.pagetitle", $content);
        $this->assertContains("user.unknown", $content);
    }

    function testPostLostPasswordSuccess()
    {
        $user = $this->userRepo->get(1);
        $this->assertEmpty($user->password_token);

        $controller = $this->container->make(Login::class);
        $_POST["lostpassword_email"] = $user->email;
        $this->setupCSRFToken("lostpassword");

        $content = $this->getControllerOutput($controller, "postLostpassword");
        $this->assertContains("lostpassword.pagetitle", $content);
        $this->assertContains("email.changepassword", $content);

        $user = $this->userRepo->get(1);
        $this->assertNotEmpty($user->password_token);

        $this->assertSame("email.changepassword.subject", $this->mailer->subject);
        $this->assertSame($user->email, $this->mailer->to);
    }

    function testGetResetPassword()
    {
        $controller = $this->container->make(Login::class);

        $user = $this->userRepo->get(1);
        $user->updatePasswordToken("theresettoken");
        $content = $this->getControllerOutput($controller, "getResetpassword", $user->id, $user->password_token);

        $this->assertContains('<h1>' . $this->lang->get("resetpassword.pagetitle") . '</h1>', $content);
        $this->assertContains('<form action="' . $this->router->getQueryString("login/resetpassword") . '"', $content);
        $this->assertContains("<p>Set the new password for user '$user->name'", $content);

        // user connected
        $controller->setLoggedInUser($user);

        $content = $this->getControllerOutput($controller, "getResetpassword", $user->id, $user->password_token);

        $this->assertEmpty(trim($content));
        $this->assertContains("user.alreadyloggedin", $this->session->getErrors());
        $this->assertRedirectTo("admin");
    }

    function testGetResetPasswordFail()
    {
        $controller = $this->container->make(Login::class);
        $user = $this->userRepo->get(1);
        $user->update([
            "password_token" => "theresettoken",
            "password_change_time" => 0, // way to late to change the password
        ]);
        $content = $this->getControllerOutput($controller, "getResetpassword", $user->id, $user->password_token);

        $this->assertEmpty(trim($content));
        $this->assertContains("user.unauthorized", $this->session->getErrors());
        $this->assertRedirectTo("login/lostpassword");
    }

    function testPostResetPasswordFail()
    {
        $controller = $this->container->make(Login::class);
        $user = $this->userRepo->get(1);

        // wrong CSRF
        $content = $this->getControllerOutput($controller, "postResetpassword", 1, "");
        $this->assertEmpty(trim($content));
        $this->assertContains("unauthorized", $this->session->getErrors());
        $this->assertRedirectTo("login/lostpassword");

        // wrong id or password token
        $this->setupCSRFToken("resetpassword");

        $content = $this->getControllerOutput($controller, "postResetpassword", 987, "whatever");
        $this->assertEmpty(trim($content));
        $this->assertContains("unauthorized", $this->session->getErrors());
        $this->assertRedirectTo("login/lostpassword");

        // invalid time
        $user->update([
            "password_token" => "theresettoken",
            "password_change_time" => 0, // way to late to change the password
        ]);
        $this->setupCSRFToken("resetpassword");

        $content = $this->getControllerOutput($controller, "postResetpassword", $user->id, $user->password_token);
        $this->assertEmpty(trim($content));
        $this->assertContains("unauthorized", $this->session->getErrors());
        $this->assertRedirectTo("login/lostpassword");

        // wrong password format
        $user->update([
            "password_token" => "theresettoken",
            "password_change_time" => time(),
        ]);
        $_POST["resetpassword"] = "A";
        $_POST["resetpassword_confirm"] = "Bz1";
        $this->setupCSRFToken("resetpassword");


        $content = $this->getControllerOutput($controller, "postResetpassword", $user->id, $user->password_token);
        $this->assertContains("fieldvalidation.passwordnotequal", $content);

        // user connected
        $this->setupCSRFToken("resetpassword");
        $controller->setLoggedInUser($user);

        $content = $this->getControllerOutput($controller, "getResetpassword", $user->id, $user->password_token);
        $this->assertEmpty(trim($content));
        $this->assertContains("user.alreadyloggedin", $this->session->getErrors());
        $this->assertRedirectTo("admin");
    }

    function testPostResetPasswordSuccess()
    {
        $user = $this->userRepo->get(1);
        $user->updatePassword("Az1");

        $this->assertTrue(password_verify("Az1", $user->password_hash));

        $user->update([
            "password_token" => "theresettoken",
            "password_change_time" => time(),
        ]);
        $_POST["resetpassword"] = "Az3rty";
        $_POST["resetpassword_confirm"] = "Az3rty";
        $this->setupCSRFToken("resetpassword");

        $controller = $this->container->make(Login::class);
        $content = $this->getControllerOutput($controller, "postResetpassword", $user->id, $user->password_token);

        $this->assertRedirectWithSuccess($content, "passwordchanged", "login");
    }

}
