<h1>{$pageTitle}</h1>

{include messages.php}

<?php
$form = new App\Form("register", $post);
$form->open($router->getQueryString("register"));
    $form->text("register_name", "name");
    $form->email("register_email", "email");
    $form->password("register_password", "password");
    $form->password("register_password_confirm", "password_confirm");
    $form->submit("", "Register");
$form->close();
?>

<a href="{queryString register/resendconfirmationemail}">Send confirmation again ?</a>
