<h1>{$pageTitle}</h1>

{include ../App/views/messages.php}

<?php
$form = new App\Form("login", $post);

$form->open(App\Route::buildQueryString("login"));
    $form->text("login_name", "Name:");
    $form->password("login_password", "Password:");
    $form->submit("", "Login");
$form->close();
?>

<a href="{queryString login/lostpassword}">Forgot Password ?</a>
