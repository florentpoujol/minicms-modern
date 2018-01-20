
<h1>{$pageTitle}</h1>

{include messages.php}

<a href="{queryString admin/menus/create}">{lang menu.createlink}</a> <br>
<br>

<table>
    <tr>
        <th>id</th>
        <th>name</th>
        <th>in use</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>

    @foreach ($allRows as $row)
    <tr>
        <td>{$row->id}</td>
        <td>{$row->name}</td>
        <td>{$row->in_use}</td>

        <td><a href="{queryString admin/menus/update/$row->id}">Edit</a></td>
        <td>
            <?php
            $form = new \App\Form("menudelete".$row->id);
            $form->open($router->getQueryString("admin/menus/delete"));
            $form->hidden("id", $row->id);
            $form->submit("", "Delete");
            $form->close();
            ?>
        </td>
    </tr>
    @endforeach
</table>

{include pagination.php}
