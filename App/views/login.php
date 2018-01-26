<h1>{$pageTitle}</h1>

{include messages.php}

<?php
$form->setup("login", $post);

$form->open($router->getQueryString("login"));
    $form->text("login_name", "Name:");
    $form->password("login_password", "Password:");
    $form->submit("", "Login");
$form->close();
?>

<a href="{queryString login/lostpassword}">Forgot Password ?</a>
