
<h1>{$pageTitle}</h1>

{include messages.php}

<a href="{queryString admin/posts/create}" class="button">{lang post.createlink}</a> <br>
<br>

<table>
    <tr>
        <th>id</th>
        <th>slug</th>
        <th>title</th>
        <th>category</th>
        <th>Editor</th>
        <th>Allow comments</th>
        <th>Nb comments</th>
        <th>Published</th>
        <th>Creation date</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>

    @foreach ($allRows as $row)
    <?php
    $category = $row->getCategory();
    if (is_object($category)) {
        $category = "$category->title ($category->id)";
    } else {
        $category = "No category attached";
    }

    $editor = $row->getUser();
    if (is_object($editor)) {
        $editor = "$editor->name ($editor->id)";
    }
    ?>
    <tr>
        <td>{$row->id}</td>
        <td>{$row->slug}</td>
        <td>{$row->title}</td>
        <td>{$category}</td>
        <td>{$editor}</td>
        <td>{$row->allow_comments}</td>
        <td><?= $row->countComments(); ?></td>
        <td>{$row->published}</td>
        <td>{$row->creation_datetime->format("Y-m-d")}</td>

        <td><a href="{queryString admin/posts/update/$row->id}" class="button button-edit">Edit</a></td>
        <td>
            <?php
            $form->setup("postdelete$row->id");
            $form->open($router->getQueryString("admin/posts/delete/$row->id"));
            $form->submit("", "Delete", ["class" => "button button-delete"]);
            $form->close();
            ?>
        </td>
    </tr>
    @endforeach
</table>

{include pagination.php}
