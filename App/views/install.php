<h1>{$pageTitle}</h1>

{include messages.php}

<?php
$form->setup("install", $post);
$form->open(""); // the install page is accessed without query string
?>
    <fieldset>
        <legend>Website</legend>
<?php
$form->text("site_title", "install.sitetitle");
?>
        <p>After installation, you will be able to go to the Config page to see more config settings.</p>
    </fieldset>

    <fieldset>
    <legend>Database</legend>
<?php
$form->text("db_host", "install.dbhost");
$form->text("db_name", "install.dbname");
$form->text("db_user", "install.dbuser");
$form->password("db_password", "instal.dbpassword");
?>
    </fieldset>

    <fieldset>
        <legend>Admin user</legend>
<?php
$form->text("name", "install.username");
$form->email("email", "install.useremail");
$form->password("password", "install.userpassword");
$form->password("password_confirmation", "install.userpasswordconfirmation");
?>
    </fieldset>
<?php
$form->submit("", "Install");
$form->close();
?>
