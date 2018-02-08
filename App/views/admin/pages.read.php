
<h1>{$pageTitle}</h1>

{include messages.php}

<a href="{queryString admin/pages/create}" class="button">{lang page.createlink}</a> <br>
<br>

<table>
    <tr>
        <th>id</th>
        <th>slug</th>
        <th>title</th>
        <th>Parent</th>
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
    $parent = $row->getParent();
    if (is_object($parent)) {
        $parent = "$parent->title ($parent->id)";
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
        <td>{$parent}</td>
        <td>{$editor}</td>
        <td>{$row->allow_comments}</td>
        <td><?= $row->countComments(); ?></td>
        <td>{$row->published}</td>
        <td>{$row->creation_datetime->format("Y-m-d")}</td>

        <td><a href="{queryString admin/pages/update/$row->id}" class="button button-edit">Edit</a></td>
        <td>
            <?php
            $form->setup("pagedelete$row->id");
            $form->open($router->getQueryString("admin/pages/delete/$row->id"));
            $form->submit("", "Delete", ["class" => "button button-delete"]);
            $form->close();
            ?>
        </td>
    </tr>
    @endforeach
</table>

{include pagination.php}
