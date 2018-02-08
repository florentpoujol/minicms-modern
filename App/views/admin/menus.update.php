
<h1>{$pageTitle}</h1>

{include messages.php}

@if ($action === "update")
Menu id: {$post["id"]} <br>
@endif
<?php
$form->setup("menu$action", $post);

$str = "admin/menus/$action";
if ($action === "update") {
    $str .= "/$post[id]";
}
$form->open($router->getQueryString($str));

$form->text("title", "title");

$form->checkbox("in_use", false, "Use this menu");

?>
    <ul>
        <li>Description of the types:
            <ul>
                <li>page, post, category: Link to the specified page, post or category which id or slug must be set in the target field. The item's title can be overriden when setting the name field</li>
                <li>blog: Link to the list of the last posts. The name of the link is by default "blog", can be overrriden by setting the name field.</li>
                <li>external: an arbitrary link to any URL. Both name and target fields must be set.</li>
                <li>folder: an item that does not link to anything, which purpose is to have children. Just set the name field</li>
                <li>homepage: define a particular page as the homepage of the site instead of the list of posts. Otherwise same as the "page" type.</li>
            </ul>
        </li>
        <li>To delete an entry (and all its children), just put nothing in both the "name" and "target" fields</li>
    </ul>

    <label>Structure:
<?php
/**
 * @param array $items
 * @param string $name
 */
function buildMenuStructure($items, $name = "")
{
    ?>
    <ul class="menu">
        <?php
        $maxId = -1;
        foreach ($items as $id => $item) {
            $itemName = $name."[$id]";
            $maxId++;
            ?>
            <li>
                <select name="<?php echo $itemName; ?>[type]">
                    <option value="page" <?php echo ($item["type"] === "page" ? "selected": null); ?>>Page</option>
                    <option value="post" <?php echo ($item["type"] === "post" ? "selected": null); ?>>Post</option>
                    <option value="category" <?php echo ($item["type"] === "category" ? "selected": null); ?>>Category</option>
                    <option value="folder" <?php echo ($item["type"] === "folder" ? "selected": null); ?>>Folder</option>
                    <option value="external" <?php echo ($item["type"] === "external" ? "selected": null); ?>>External</option>
                    <option value="homepage" <?php echo ($item["type"] === "homepage" ? "selected": null); ?>>Home page</option>
                </select>

                <input type="text" name="<?php echo $itemName; ?>[name]" value="{$item["name"]}" placeholder="name">

                <input type="text" name="<?php echo $itemName; ?>[target]" value="{$item["target"]}" placeholder="target">

                <?php
                if (! isset($item["children"])) {
                    $item["children"] = [];
                }

                buildMenuStructure($item["children"], $itemName."[children]");
                ?>
            </li>
            <?php
        }

        $maxId++;
        $itemName = $name."[$maxId]";
        ?>
        <li>
            <select name="<?php echo $itemName; ?>[type]">
                <option value="page">Page</option>
                <option value="post">Post</option>
                <option value="category">Category</option>
                <option value="folder">Folder</option>
                <option value="external">External</option>
                <option value="home">Home</option>
            </select>
            <input type="text" name="<?php echo $itemName; ?>[name]" placeholder="name">
            <input type="text" name="<?php echo $itemName; ?>[target]" placeholder="target">
        </li>
    </ul>
    <?php
}

buildMenuStructure($post["structure"], "structure");

?>
    </label>
<?php

$form->textarea("json_structure", ["label" => "JSON structure", "value" => "{}"]);

if ($action === "update") {
    echo "Creation date: " . $post["creation_datetime"]->format("Y-m-d") . "<br>";
}

$form->submit("", "$action menu", ["class" => "button"]);
$form->close();
?>
