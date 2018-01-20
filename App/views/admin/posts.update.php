
<h1>{$pageTitle}</h1>

{include messages.php}

@if ($action === "update")
Page id: {$post["id"]} <br>
@endif
<?php
if (! isset($post["user_id"])) {
    $post["user_id"] = $this->user->id;
}
$form = new \App\Form("post$action", $post);

$str = "admin/posts/$action";
if ($action === "update") {
    $str .= "/".$post["id"];
}
$form->open($router->getQueryString($str));

$form->text("slug", "slug");
$form->text("title", "title");

$form->textarea("content", ["cols" => 50, "rows" => 20, "label" => "content"]);

// category id
$categories = \App\Entities\Category::getAll();
$options = [];
foreach ($categories as $cat) {
    $options[$cat->id] = $cat->title." ($cat->id)";
}
$form->select("category_id", $options, "category: ");

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

// published
$form->checkbox("published", true, "published");

// allow comments
$form->checkbox("allow_comments", null, "allowcomments");

if ($action === "update") {
    echo "Creation date: ".$post["creation_datetime"];
    $form->hidden("id", $post["id"]);
}
$form->submit("", "$action page");
$form->close();
?>
