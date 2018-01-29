<?php

namespace Tests\Controllers;

use App\Controllers\Register;
use App\Entities\User;
use Tests\DatabaseTestCase;

class RegisterTest extends DatabaseTestCase
{
    function testGetRegister()
    {
        $controller = $this->container->make(Register::class);

        $content = $this->getControllerOutput($controller, "getRegister");

        $this->assertContains('<h1>' . $this->lang->get("register.pagetitle") . '</h1>', $content);
        $this->assertContains('<form action="' . $this->router->getQueryString("register") . '"', $content);
        $this->assertContains('<a href="' . $this->router->getQueryString("register/resendconfirmationemail") . '">Send confirmation email again ?</a>', $content);

        // logged in user
        $controller->setLoggedInUser($this->userRepo->get(2));
        $content = $this->getControllerOutput($controller, "getRegister");
        $this->assertRedirectWithError($content, "user.alreadyloggedin", "admin");
    }

    function testPostRegisterFail()
    {
        $controller = $this->container->make(Register::class);

        // wrong CSRF
        $content = $this->getControllerOutput($controller, "postRegister");
        $this->assertContains("csrffail", $content);

        // wrong fields format
        $_POST["register_name"] = "a";
        $_POST["register_email"] = "a";
        $_POST["register_password"] = "a";
        $_POST["register_password_confirmation"] = "a";
        $this->setupCSRFToken("register");

        $content = $this->getControllerOutput($controller, "postRegister");
        $this->assertContains("register.pagetitle", $content);
        $this->assertContains("fieldvalidation.name", $content);
        $this->assertContains("fieldvalidation.email", $content);
        $this->assertContains("fieldvalidation.password", $content);

        // username already exists
        $user = $this->userRepo->get(2);
        $_POST["register_name"] = $user->name;
        $_POST["register_email"] = $user->email;
        $_POST["register_password"] = "Az1";
        $_POST["register_password_confirm"] = "Az1";
        $this->setupCSRFToken("register");

        $content = $this->getControllerOutput($controller, "postRegister");
        $this->assertContains("register.pagetitle", $content);
        $this->assertContains("user.namenotunique", $content);

        // logged in user
        $controller->setLoggedInUser($this->userRepo->get(2));
        $content = $this->getControllerOutput($controller, "getRegister");
        $this->assertRedirectWithError($content, "user.alreadyloggedin", "admin");
    }

    function testPostRegisterSuccess()
    {
        $user = $this->userRepo->get(1);
        $user->updatePassword("Az1");
        $_POST["register_name"] = "NewUser";
        $_POST["register_email"] = "email@email.fr";
        $_POST["register_password"] = "Az1";
        $_POST["register_password_confirmation"] = "Az1";
        $this->setupCSRFToken("register");

        $this->assertSame(3, $this->userRepo->countAll());

        $controller = $this->container->make(Register::class);
        $content = $this->getControllerOutput($controller, "postRegister");

        // check messages and redirect
        $successes = $this->session->getSuccesses();
        $this->assertContains("user.created", $successes);
        $this->assertContains("email.confirmemail", $successes);
        $this->assertRedirectWithSuccess($content, "", "login");

        // check user
        $this->assertSame(4, $this->userRepo->countAll());
        $user = $this->userRepo->get(["name" => "NewUser"]);
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame("email@email.fr", $user->email);
        $this->assertNotEmpty($user->email_token);

        // check email
        $this->assertSame("email@email.fr", $this->mailer->to);
        $this->assertSame("email.confirmemail.subject", $this->mailer->subject);
        $this->assertContains("register/confirmemail/$user->id/$user->email_token", $this->mailer->body);
    }

    function testGetConfirmEmail()
    {
        $user = $this->userRepo->get(3);

        // empty token
        $controller = $this->container->make(Register::class);
        $content = $this->getControllerOutput($controller, "getConfirmEmail", $user->id, "");
        $this->assertRedirectWithError($content, "user.unauthorized", "");

        // wrong id or token
        $content = $this->getControllerOutput($controller, "getConfirmEmail", $user->id, "nottherighttoken");
        $this->assertRedirectWithError($content, "user.unauthorized", "");

        $user->updateEmailToken("theemailtoken");
        $content = $this->getControllerOutput($controller, "getConfirmEmail", 987, "theemailtoken");
        $this->assertRedirectWithError($content, "user.unauthorized", "");

        // success
        $content = $this->getControllerOutput($controller, "getConfirmEmail", $user->id, "theemailtoken");
        $this->assertRedirectWithSuccess($content, "user.emailconfirmed", "login");
        $user = $this->userRepo->get($user->id);
        $this->assertEmpty($user->email_token);

        // logged in user
        $controller->setLoggedInUser($this->userRepo->get(2));
        $content = $this->getControllerOutput($controller, "getConfirmEmail", 2, "");
        $this->assertRedirectWithError($content, "user.alreadyloggedin", "admin");
    }

    function testGetResendConfirmationEmail()
    {
        $controller = $this->container->make(Register::class);
        $content = $this->getControllerOutput($controller, "getResendConfirmationEmail");

        $this->assertContains("<h1>resendconfirmationemail.pagetitle</h1>", $content);
        $this->assertContains('<form action="' . $this->router->getQueryString("register/resendconfirmationemail") . '"', $content);

        // logged in user
        $controller->setLoggedInUser($this->userRepo->get(2));
        $content = $this->getControllerOutput($controller, "getResendConfirmationEmail");
        $this->assertRedirectWithError($content, "user.alreadyloggedin", "admin");
    }

    function testPostResendConfirmationEmailFail()
    {
        $controller = $this->container->make(Register::class);

        // wrong CSRF
        $content = $this->getControllerOutput($controller, "postResendConfirmationEmail");
        $this->assertContains("csrffail", $content);

        // wrong email format
        $_POST["confirm_email"] = "a";
        $this->setupCSRFToken("resendconfirmationemail");

        $content = $this->getControllerOutput($controller, "postResendConfirmationEmail");
        $this->assertContains("fieldvalidation.email", $content);

        // unknown user
        $_POST["confirm_email"] = "email@unknown.fr";
        $this->setupCSRFToken("resendconfirmationemail");

        $content = $this->getControllerOutput($controller, "postResendConfirmationEmail");
        $this->assertContains("user.unknown", $content);

        // user already activated
        $user = $this->userRepo->get(2);
        $_POST["confirm_email"] = $user->email;
        $this->setupCSRFToken("resendconfirmationemail");

        $content = $this->getControllerOutput($controller, "postResendConfirmationEmail");
        $this->assertRedirectWithError($content, "user.alreadyactivated", "login");

        // logged in user
        $controller->setLoggedInUser($this->userRepo->get(2));
        $content = $this->getControllerOutput($controller, "getResendConfirmationEmail");
        $this->assertRedirectWithError($content, "user.alreadyloggedin", "admin");
    }

    function testPostResendConfirmationEmailSuccess()
    {
        $user = $this->userRepo->get(2);
        $user->updateEmailToken("theemailtoken");
        $this->assertNotEmpty($user->email_token);

        $_POST["confirm_email"] = $user->email;
        $this->setupCSRFToken("resendconfirmationemail");

        $controller = $this->container->make(Register::class);
        $content = $this->getControllerOutput($controller, "postResendConfirmationEmail");

        $this->assertRedirectWithSuccess($content, "email.confirmemail", "register");

        // check email
        $this->assertSame($user->email, $this->mailer->to);
        $this->assertSame("email.confirmemail.subject", $this->mailer->subject);
        $this->assertContains("register/confirmemail/$user->id/$user->email_token", $this->mailer->body);
    }
}
