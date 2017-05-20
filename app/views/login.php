<h1>{pageTitle}</h1>

<?php
include "messages.php";

$form = new App\Form("login", ["login_name" => "default login name2"]);

$form->open("?c=login");

$form->text("login_name", "Name:");
$form->br();
$form->password("login_password", "Password:");
$form->br(2);
$form->submit("", "Login");

$form->close();
?>

<a href="?c=login&a=lostpassword">Forgot Password ?</a>
