
<h1>{$pageTitle}</h1>

<?php
$uploadPath = $config->get("upload_path") . "/";
if (! is_writable($uploadPath)) {
    $session->addError("file.uploadfoldernotwritable");
}
?>

{include messages.php}

@if ($action === "update")
Media id: {$post["id"]} <br>
@endif
<?php
$form->setup("media$action", $post);

$str = "admin/medias/$action";
if ($action === "update") {
    $str .= "/$post[id]";
}
$form->open($router->getQueryString($str), "post", true);

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
        echo '<a href="uploads/' . $post["filename"] . '">' . $post["filename"] . '</a> <br><br>';
    } else {
        echo '<img src="uploads/' . $post["filename"] . '" height="200px"> <br><br>';
    }

    // user id
    $options = [];
    foreach ($users as $user) {
        $options[$user->id] = "$user->name ($user->id)";
    }
    $form->select("user_id", $options, "User: ");

    echo "Creation date: " . $post["creation_datetime"]->format("Y-m-d") . "<br>";
    $form->hidden("id", $post["id"]);
}
$form->submit("", "$action media", ["class" => "button"]);
$form->close();
?>
