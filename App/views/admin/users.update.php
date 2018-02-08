
<h1>{$pageTitle}</h1>

{include messages.php}

@if ($action === "update")
User id: {$post["id"]} <br>
@endif
<?php
if ($action === "create") {
    $post["password_change_time"] = 0;
}
$form->setup("user$action", $post);

$str = "admin/users/$action";
if ($action === "update") {
    $str .= "/$post[id]";
}
$form->open($router->getQueryString($str));

$form->text("name", "name");
$form->email("email", "email");
$form->password("password", "password");
$form->password("password_confirmation", "password_confirmation");

if ($user->isAdmin()) {
    $options = [
        "commenter" => "Commenter",
        "writer" => "Writer",
        "admin" => "Admin",
    ];
    $form->select("role", $options, "Roles: ");
    if ($action === "update") {
        $form->text("email_token", "email token");
        $form->text("password_token", "password token");
        $form->number("password_change_time", "password change time");
    }
} else {
    echo "Role: " . ucfirst($post["role"]) . "<br>";
}

$form->submit("", "$action user", ["class" => "button"]);
$form->close();
?>
