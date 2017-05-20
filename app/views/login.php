<?php
if (isset($loginName) === false) $loginName = "";
?>
<h1><?php echo $pageTitle; ?></h1>

<?php include "messages.php"; ?>

<form action="?c=login" method="POST">
    <label>Name : <input type="text" name="login_name" value="<?php echo $loginName; ?>" required></label> <br>
    <label>Password : <input type="password" name="login_password" required></label> <br>

    <input type="submit" name="login" value="Login">
</form>

<a href="?c=login&a=lostpassword">Forgot Password ?</a>