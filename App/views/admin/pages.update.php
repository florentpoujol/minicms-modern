<?php
use \App\Entities\Page;
?>

<h1>{$pageTitle}</h1>

{include messages.php}

@if ($action === "update")
Page id: {$post["id"]} <br>
@endif
<?php
$form = new \App\Form("page$action", $post);

$str = "admin/pages/$action";
if ($action === "update") {
    $str .= "/".$post["id"];
}
$form->open(\App\Router::getQueryString($str));

$form->text("slug", "slug");
$form->text("title", "title");

$form->textarea("content", ["cols" => 50, "rows" => 20, "label" => "content"]);

// parent page id
$pages = Page::getAll(["parent_page_id" => null]);
$options = ["0" => "None"];
foreach ($pages as $page) {
    if ($action === "update" && $post["id"] === $page->id)
        continue;

    $options[$page->id] = $page->title." ($page->id)";
}
$form->select("parent_page_id", $options, "parent page: ");

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
