<h1>{$pageTitle}</h1>

{include messages.php}

<p>Set the new password for user '{$userName}' below :</p>

<?php
$form = new App\Form("resetpassword", $post);

$form->open($router->getQueryString("login/resetpassword"));
    $form->text("resetpassword", "password");
    $form->password("resetpassword_confirm", "password_confirm");
    $form->submit("", "Reset Password");
$form->close();
?>
