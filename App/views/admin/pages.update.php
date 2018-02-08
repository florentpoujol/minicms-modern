
<h1>{$pageTitle}</h1>

{include messages.php}

@if ($action === "update")
Page id: {$post["id"]} <br>
@endif
<?php
$form->setup("page$action", $post);

$str = "admin/pages/$action";
if ($action === "update") {
    $str .= "/$post[id]";
}
$form->open($router->getQueryString($str));

$form->text("slug", "slug");
$form->text("title", "title");

$form->textarea("content", ["cols" => 50, "rows" => 20, "label" => "content"]);

// parent page id
$options = ["0" => "None"];
foreach ($parentPages as $page) {
    if ($action === "update" && $post["id"] === $page->id) {
        continue;
    }

    $options[$page->id] = "$page->title ($page->id)";
}
$form->select("parent_page_id", $options, "parent page: ");

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
    echo "Creation date: " . $post["creation_datetime"]->format("Y-m-d") . "<br>";
}
$form->submit("", "$action page", ["class" => "button"]);
$form->close();
?>
