<h1>{$pageTitle}</h1>

{include messages.php}

<p>If you forgot your password, you can fill the form below, we will send an email so that you can change your password.</p>

<?php
$form->setup("lostpassword", $post);
$form->open($router->getQueryString("login/lostpassword"));
    $form->email("lostpassword_email", "Email: ");
    $form->recaptcha();
    $form->submit("", "Request password change");
$form->close();
?>
