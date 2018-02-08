<h1>{$pageTitle}</h1>

{include messages.php}

<?php
$form->setup("resendconfirmationemail", $post);
$form->open($router->getQueryString("register/resendconfirmationemail"));
    $form->email("confirm_email", "email");
    $form->recaptcha();
    $form->submit("", "Resend email");
$form->close();
?>
