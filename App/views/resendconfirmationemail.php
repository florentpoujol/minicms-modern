<h1>{$pageTitle}</h1>

{include messages.php}

<?php
$form = new App\Form("resendconfirmationemail", $post);
$form->open($router->getQueryString("register/resendconfirmationemail"));
    $form->email("confirm_email", "email");
    $form->submit("", "Resend email");
$form->close();
?>
