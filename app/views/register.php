<h1><?php echo $pageTitle; ?></h1>

<?php include "messages.php"; ?>

<form action="?c=register" method="POST">
    <label>Name : <input type="text" name="register_name" value="<?php echo $name; ?>" required></label> <br>
    <label>Email : <input type="email" name="register_email" value="<?php echo $email; ?>" required></label> <br>
    <label>Password : <input type="password" name="register_password" required></label> <br>
    <label>Password confirmation: <input type="password" name="register_password_confirm" required></label> <br>

    <input type="submit" value="Register">
</form>

<a href="?c=register&a=resendconfirmemail">Send confirmation again ?</a>