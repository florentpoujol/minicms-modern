<h1>{pageTitle}</h1>

<?php
include "messages.php";

$form = new App\Form("register", $post);
$form->open("?c=register");
    $form->text("register_name", "name");
    $form->email("register_email", "email");
    $form->password("register_password", "password");
    $form->password("register_password_confirm", "password_confirm");
    $form->submit("", "Register");
$form->close();
?>

<a href="?c=register&a=resendconfirmemail">Send confirmation again ?</a>
