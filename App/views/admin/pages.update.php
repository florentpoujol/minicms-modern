<?php
use \App\Entities\User;
use \App\Entities\Page;
?>

<h1>{$pageTitle}</h1>

{include ../App/views/messages.php}

@if ($action === "update")
Page id: {$post["id"]} <br>
@endif
<?php
$form = new \App\Form("page$action", $post);

$str = "admin/pages/$action";
if ($action === "update") {
    $str .= "/".$post["id"];
}
$form->open(\App\Route::buildQueryString($str));

$form->text("slug", "slug");
$form->text("title", "title");

// user id
$users = array_merge(
        User::getAll(["role" => "admin"]),
        User::getAll(["role" => "writer"])
);
$options = [];
foreach ($users as $user) {
    $options[$user->id] = $user->name." ($user->id)";
}
$form->select("user_id", $options, "owner: ");

// parent page id
$pages = Page::getAll(["parent_page_id" => null]);
$options = ["" => "None"];
foreach ($pages as $page) {
    $options[$page->id] = $page->title." ($page->id)";
}
$form->select("parent_page_id", $options, "parent page: ");

// published
$form->checkbox("published", true, "published");

// allow comments
$form->checkbox("allow_comments", null, "allowcomments");


if ($action === "update") {
    echo $post["creation_datetime"];
    $form->hidden("id", $post["id"]);
}
$form->submit("", "$action page");
$form->close();
?>
