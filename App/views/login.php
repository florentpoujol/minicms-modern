<h1>{$pageTitle}</h1>

{include messages.php}

<a href="{queryString register}">Register first !</a> <br> <br>

<?php
$form->setup("login", $post);

$form->open($router->getQueryString("login"));
    $form->text("login_name", "Name:");
    $form->password("login_password", "Password:");
    $form->recaptcha();
    $form->submit("", "Login");
$form->close();
?>

<a href="{queryString login/lostpassword}">Forgot Password ?</a>
