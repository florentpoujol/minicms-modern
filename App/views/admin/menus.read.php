
<h1>{$pageTitle}</h1>

{include messages.php}

<a href="{queryString admin/menus/create}" class="button">{lang menu.createlink}</a> <br>
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
        <td>{$row->title}</td>
        <td>{$row->in_use}</td>

        <td><a href="{queryString admin/menus/update/$row->id}" class="button button-edit">Edit</a></td>
        <td>
            <?php
            $form->setup("menudelete$row->id");
            $form->open($router->getQueryString("admin/menus/delete/$row->id"));
            $form->submit("", "Delete", ["class" => "button button-delete"]);
            $form->close();
            ?>
        </td>
    </tr>
    @endforeach
</table>

{include pagination.php}
