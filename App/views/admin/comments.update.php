
<h1>{$pageTitle}</h1>

{include messages.php}

Comment id: {$post["id"]} <br>
<?php
$form->setup("commentupdate", $post);
$form->open($router->getQueryString("admin/comments/update/$post[id]"));

$form->textarea("content", ["rows" => 7, "cols" => 50, "label" => "content"]);

// user id
$options = [];
foreach ($users as $user) {
    $options[$user->id] = "$user->name ($user->id)";
}
$form->select("user_id", $options, "User: ");

echo "Creation date: " . $post["creation_datetime"]->format("Y-m-d") . "<br><br>";
// $form->hidden("id", $post["id"]);

$form->submit("", "update comment", ["class" => "button"]);
$form->close();
?>
