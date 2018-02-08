
<h1>{$pageTitle}</h1>

{include messages.php}

@if ($action === "update")
Post id: {$post["id"]} <br>
@endif
<?php
if (! isset($post["user_id"])) {
    $post["user_id"] = $user->id; // why ?
}
$form->setup("post$action", $post);

$str = "admin/posts/$action";
if ($action === "update") {
    $str .= "/$post[id]";
}
$form->open($router->getQueryString($str));

$form->text("slug", "slug");
$form->text("title", "title");

$form->textarea("content", ["cols" => 50, "rows" => 20, "label" => "content"]);

// category id
$options = [];
foreach ($categories as $category) {
    $options[$category->id] = "$category->title ($category->id)";
}
$form->select("category_id", $options, "category: ");

// user id
$options = [];
foreach ($users as $user) {
    $options[$user->id] = "$user->name ($user->id)";
}
$form->select("user_id", $options, "User: ");

// published
$form->checkbox("published", true, "published");

// allow comments
$form->checkbox("allow_comments", $config->get("allow_comments"), "allowcomments");

if ($action === "update") {
    echo "Creation date: " . $post["creation_datetime"]->format("Y-M-d") . "<br>";
}
$form->submit("", "$action post", ["class" => "button"]);
$form->close();
?>
