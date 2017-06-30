<h1>{$pageTitle}</h1>

{include ../App/views/messages.php}

<?php
$form = new App\Form("resendconfirmationemail", $post);
$form->open(\App\Route::buildQueryString("register/resendconfirmationemail"));
    $form->email("confirm_email", "email");
    $form->submit("", "Resend email");
$form->close();
?>
