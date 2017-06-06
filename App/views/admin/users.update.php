
<h1>{pageTitle}</h1>

<?php include "../App/views/messages.php"; ?>

@if ($action === "update")
User id: {user->id} <br>
@endif
<?php
$form = new \App\Form("user$action", $post);
$form->open(\App\Route::buildQueryString("admin/users/$action/".$post["id"]));
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

    $form->hidden("id", $post["id"]);
    $form->submit("", "Create user");
$form->close();
?>
