
<h1>{$pageTitle}</h1>

{include ../App/views/messages.php}

<a href="{queryString admin/posts/create}">{lang post.createlink}</a> <br>
<br>

<table>
    <tr>
        <th>id</th>
        <th>slug</th>
        <th>title</th>
        <th>category</th>
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
        $category = $category->title." (".$category->id.")";
    }
    ?>
    <tr>
        <td>{$row->id}</td>
        <td>{$row->slug}</td>
        <td>{$row->title}</td>
        <td>{$category}</td>
        <td>{$row->allow_comments}</td>
        <td><?php echo \App\Entities\Comment::countAll(["post_id" => $row->id]); ?></td>
        <td>{$row->published}</td>
        <td>{$row->creation_datetime}</td>

        <td><a href="{queryString admin/posts/update/$row->id}">Edit</a></td>
        <td>
            <?php
            $form = new \App\Form("postdelete".$row->id);
            $form->open(\App\Route::buildQueryString("admin/posts/delete"));
            $form->hidden("id", $row->id);
            $form->submit("", "Delete");
            $form->close();
            ?>
        </td>
    </tr>
    @endforeach
</table>

{include ../App/views/pagination.php}
