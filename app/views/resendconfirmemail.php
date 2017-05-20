<h1><?php echo $pageTitle; ?></h1>

<?php include "messages.php"; ?>

<form action="" method="POST">
    <label>Email : <input type="email" name="confirm_email" value="<?php echo $email; ?>" required></label> <br>

    <input type="submit" value="Resend email">
</form>
