
<h1>{$pageTitle}</h1>

{include ../App/views/messages.php}

<a href="{queryString admin/pages/create}">{lang page.createlink}</a> <br>
<br>

<table>
    <tr>
        <th>id</th>
        <th>slug</th>
        <th>title</th>
        <th>Creator</th>
        <th>creation date</th>
        <th>Nb comments</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>

    @foreach ($allRows as $row)
    <tr>
        <td>{$row->id}</td>
        <td>{$row->slug}</td>
        <td>{$row->title}</td>
        <td>{$row->getUser()->name} ({$row->getUser()->id})</td>
        <td>{$row->creation_datetime}</td>
        <td><?php echo \App\Entities\Comment::countAll(["page_id" => $row->id]); ?></td>

        <td><a href="{queryString admin/pages/update/$row->id}">Edit</a></td>
        <td>
            <?php
            $form = new \App\Form("pagedelete".$row->id);
            $form->open(\App\Route::buildQueryString("admin/pages/delete"));
            $form->hidden("id", $row->id);
            $form->submit("", "Delete");
            $form->close();
            ?>
        </td>
    </tr>
    @endforeach
</table>

{include ../App/views/pagination.php}
