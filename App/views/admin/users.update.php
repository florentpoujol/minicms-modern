
<h1>{pageTitle}</h1>

<?php include "../App/views/messages.php"; ?>

@if ($action === "update")
User id: {post["id"]} <br>
@endif
<?php
$form = new \App\Form("user$action", $post);
$str = "admin/users/$action";
if ($action == "update") {
    $str .= "/".$post["id"];
}
$form->open(\App\Route::buildQueryString($str));
    $form->text("name", "name");
    $form->email("email", "email");
    $form->password("password", "password");
    $form->password("password_confirmation", "password_confirm");

    $options = [
        "commenter" => "Commenter",
        "writer" => "Writer",
        "admin" => "Admin",
    ];
    $form->select("role", $options, "Roles: ");

    if ($action === "update") {
        $form->hidden("id", $post["id"]);
    }
    $form->submit("", "$action user");
$form->close();
?>
