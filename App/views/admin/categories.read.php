
<h1>{$pageTitle}</h1>

{include messages.php}

<a href="{queryString admin/categories/create}" class="button">{lang categories.createlink}</a> <br>
<br>

<table>
    <tr>
        <th>id</th>
        <th>slug</th>
        <th>title</th>
        <th>post count</th>
        <th>Edit</th>
        @if ($user->isAdmin())
        <th>Delete</th>
        @endif
    </tr>

    @foreach ($allRows as $row)
    <tr>
        <td>{$row->id}</td>
        <td>{$row->slug}</td>
        <td>{$row->title}</td>
        <td><?= $row->countPosts(); ?></td>
        <td><a href="{queryString admin/categories/update/$row->id}" class="button button-edit">Edit</a></td>

        @if ($user->isAdmin())
        <td>
        <?php
        $form->setup("categorydelete$row->id");
        $form->open($router->getQueryString("admin/categories/delete/$row->id"));
        $form->submit("", "Delete", ["class" => "button button-delete"]);
        $form->close();
        ?>
        </td>
        @endif
    </tr>
    @endforeach
</table>

{include pagination.php}
