<h1>{$pageTitle}</h1>

{include messages.php}

<a href="{queryString admin/medias/create}" class="button">{lang media.createlink}</a> <br>
<br>

<table>
    <tr>
        <th>id</th>
        <th>slug</th>
        <th>filename / preview</th>
        <th>user</th>
        <th>creation datetime</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>

    <?php
    $uploadPath = $config->get("upload_path");
    ?>
    @foreach ($allRows as $row)
    <?php
    $owner = $row->getUser();
    $owner = "$owner->name ($owner->id)";

    $ext = pathinfo($row->filename, PATHINFO_EXTENSION);
    ?>
    <tr>
        <td>{$row->id}</td>
        <td>{$row->slug}</td>
        <td>
            @if ($ext === "zip" || $ext === "pdf")
            <a href="uploads/{$row->filename}">{$row->filename}</a> <br>
            @else
            {$row->filename} <br>
            <img src="uploads/{$row->filename}" height="200px"> <br>
            @endif
        </td>
        <td>{$owner}</td>
        <td>{$row->creation_datetime->format("Y-m-d")}</td>
        <td><a href="{queryString admin/medias/update/$row->id}" class="button button-edit">Edit</a></td>
        <td>
            <?php
            $form->setup("mediadelete".$row->id);
            $form->open($router->getQueryString("admin/medias/delete/$row->id"));
            $form->submit("", "Delete", ["class" => "button button-delete"]);
            $form->close();
            ?>
        </td>
    </tr>
    @endforeach
</table>

{include pagination.php}
