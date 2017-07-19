
<h1>{$pageTitle}</h1>

<?php
$uploadPath = \App\App::$uploadPath;
if (! is_writable($uploadPath)) {
    \App\Messages::addError("file.uploadfoldernotwritable");
}
?>

{include ../App/views/messages.php}

@if ($action === "update")
Media id: {$post["id"]} <br>
@endif
<?php
$form = new \App\Form("media$action", $post);

$str = "admin/medias/$action";
if ($action === "update") {
    $str .= "/".$post["id"];
}
$form->open(\App\Route::buildQueryString($str), "post", true);

$form->text("slug", "slug");

echo "<br>";
if ($action === "create") {
    $form->file("upload_file", ["label" => "File:"]);
    ?>
    <p>Allowed extensions: .jpg, .jpeg, .png, .pdf or .zip</p>
    <?php
} else { // update
    // show file preview
    $ext = pathinfo($post["filename"], PATHINFO_EXTENSION);
    echo "file preview: <br>";
    if ($ext === "zip" || $ext === "pdf") {
        echo '<a href="' . $uploadPath . $post["filename"] . '">' . $post["filename"] . '</a> <br><br>';
    } else {
        echo '<img src="' . $uploadPath . $post["filename"] . '" height="200px"> <br><br>';
    }

    // user id
    $users = array_merge(
        \App\Entities\User::getAll(["role" => "admin"]),
        \App\Entities\User::getAll(["role" => "writer"])
    );
    $options = [];
    foreach ($users as $user) {
        $options[$user->id] = $user->name." ($user->id)";
    }
    $form->select("user_id", $options, "User: ");

    echo "Creation date: ".$post["creation_datetime"];
    $form->hidden("id", $post["id"]);
}
$form->submit("", "$action media");
$form->close();
?>
