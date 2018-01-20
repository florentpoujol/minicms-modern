
<h1>{$pageTitle}</h1>

{include messages.php}

<a href="{queryString admin/categories/create}">{lang categories.createlink}</a> <br>
<br>

<table>
    <tr>
        <th>id</th>
        <th>slug</th>
        <th>title</th>
        <th>post count</th>
        <th>Edit</th>
        @if ($this->user->isAdmin())
        <th>Delete</th>
        @endif
    </tr>

    @foreach ($allRows as $row)
    <tr>
        <td>{$row->id}</td>
        <td>{$row->slug}</td>
        <td>{$row->title}</td>
        <td><?php echo \App\Entities\Post::countAll(["category_id" => $row->id]); ?></td>
        <td><a href="{queryString admin/categories/update/$row->id}">Edit</a></td>

        @if ($this->user->isAdmin())
        <td>
        <?php
        $form = new \App\Form("categorydelete".$row->id);
        $form->open($router->getQueryString("admin/categories/delete"));
        $form->hidden("id", $row->id);
        $form->submit("", "Delete");
        $form->close();
        ?>
        </td>
        @endif
    </tr>
    @endforeach
</table>

{include pagination.php}
