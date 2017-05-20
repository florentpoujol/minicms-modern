<h1><?php echo $pageTitle; ?></h1>

<?php include "messages.php"; ?>

<p>If you forgot your password, you can fill the form below, we will send an email so that you can change your password.</p>
<form action="" method="POST">
    <label>Email : <input type="email" name="forgot_password_email" required></label> <br>
    <input type="submit" value="Request password change">
</form>