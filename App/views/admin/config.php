
<h1>{$pageTitle}</h1>

{include messages.php}

<p>Password are only updated when their fields are filled.</p>
<?php
$form->setup("config", $configArray);

$form->open($router->getQueryString("admin/config"));
?>
<h2>Site</h2>
<?php
$form->text("site_title", "config.sitetitle");
$form->checkbox("use_nice_url", false, "config.useniceurl");
$form->checkbox("allow_comments", false, "config.allowcomments");
$form->checkbox("allow_registration", false, "config.allowregistration");
$form->text("recaptcha_secret", "config.recaptcha_secret");
$form->text("recaptcha_site_key", "config.recaptcha_site_key");
$form->number("items_per_page", "config.iemsperpage");

?>
<h2>Email</h2>
<?php
$form->email("mailer_from_address", "config.mailerfromaddress");
$form->text("mailer_from_name", "config.mailerfromname");
$form->text("smtp_host", "config.smtphost");
$form->text("smtp_user", "config.smtpuser");
$form->password("smtp_password", "config.smtppassword");
$form->number("smtp_port", "config.smtpport");
echo "<br>";
$form->email("test_email", "config.testemail");
$form->submit("test_email_submit", "config.testemailsubmit", ["class" => "button"]);

?>
<h2>Database</h2>

<p>
    Warning ! <br>
    Making a mistake when updating any of the fields below WILL render the site inaccessible. <br>
    You will have to fix the mistake by opening the file config file directly on the server.
</p>
<?php
$form->text("db_host", "config.dbhost");
$form->text("db_name", "config.dbname");
$form->text("db_user", "config.dbuser");
$form->password("db_password", "config.dbpassword");

$form->submit("", "Update configuration", ["class" => "button button-edit"]);
$form->close();
?>
