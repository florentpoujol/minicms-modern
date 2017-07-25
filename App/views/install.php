
<h1>{$pageTitle}</h1>

{include ../App/views/messages.php}

<?php
$form = new App\Form("install", $post);

$form->open("");
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
$form->password("password_confirm", "install.userpasswordconfirmation");
?>
    </fieldset>
<?php
$form->submit("", "Install");
$form->close();
?>
