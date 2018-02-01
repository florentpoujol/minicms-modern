
<h1>{$pageTitle}</h1>

{include messages.php}

@if ($action === "update")
Category id: {$post["id"]} <br>
@endif
<?php
$form->setup("category$action", $post);
$str = "admin/categories/$action";
if ($action === "update") {
    $str .= "/$post[id]";
}
$form->open($router->getQueryString($str));

$form->text("title", "title");
$form->text("slug", "slug");

$form->submit("", "$action category");
$form->close();
?>
