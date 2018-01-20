
<h1>{$pageTitle}</h1>

{include messages.php}

<a href="{queryString admin/pages/create}">{lang page.createlink}</a> <br>
<br>

<table>
    <tr>
        <th>id</th>
        <th>slug</th>
        <th>title</th>
        <th>Parent</th>
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
        $parent = $parent->title." (".$parent->id.")";
    } else {
        $parent = "";
    }
    ?>
    <tr>
        <td>{$row->id}</td>
        <td>{$row->slug}</td>
        <td>{$row->title}</td>
        <td>{$parent}</td>
        <td>{$row->allow_comments}</td>
        <td><?php echo \App\Entities\Comment::countAll(["page_id" => $row->id]); ?></td>
        <td>{$row->published}</td>
        <td>{$row->creation_datetime}</td>

        <td><a href="{queryString admin/pages/update/$row->id}">Edit</a></td>
        <td>
            <?php
            $form = new \App\Form("pagedelete".$row->id);
            $form->open($router->getQueryString("admin/pages/delete"));
            $form->hidden("id", $row->id);
            $form->submit("", "Delete");
            $form->close();
            ?>
        </td>
    </tr>
    @endforeach
</table>

{include pagination.php}
