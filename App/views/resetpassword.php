<h1>{pageTitle}</h1>

<?php include "messages.php"; ?>

<p>Set the new password for user '{userName}' below :</p>

<?php
$form = new App\Form("resetpassword", $post);

$form->open("");
    $form->text("resetpassword", "password");
    $form->password("resetpassword_confirm", "password_confirm");
    $form->submit("", "Reset Password");
$form->close();
?>
