
<h1>{pageTitle}</h1>

{include ../App/views/messages.php}

@if ($action === "update")
User id: {post["id"]} <br>
@endif
<?php
$form = new \App\Form("user$action", $post);

$str = "admin/users/$action";
if ($action === "update") {
    $str .= "/".$post["id"];
}
$form->open(\App\Route::buildQueryString($str));

    $form->text("name", "name");
    $form->email("email", "email");
    $form->password("password", "password");
    $form->password("password_confirmation", "password_confirm");

    if ($this->user->isAdmin()) {
        $options = [
            "commenter" => "Commenter",
            "writer" => "Writer",
            "admin" => "Admin",
        ];
        $form->select("role", $options, "Roles: ");
    } else {
        echo "Role: ".ucfirst($this->user->role);
    }

    if ($action === "update") {
        $form->hidden("id", $post["id"]);
    }
    $form->submit("", "$action user");
$form->close();
?>
