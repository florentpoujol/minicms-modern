<h1>{pageTitle}</h1>

<?php
include "messages.php";

$form = new App\Form("resentconfirmemail", $post);
$form->open("?c=register&a=resentconfirmemail");
    $form->email("confirm_email", "email");
    $form->submit("", "Resend email");
$form->close();
?>
