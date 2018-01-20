<h1>{$pageTitle}</h1>

{include messages.php}

<a href="{queryString admin/medias/create}">{lang media.createlink}</a> <br>
<br>

<table>
    <tr>
        <th>id</th>
        <th>slug</th>
        <th>filename</th>
        <th>preview</th>
        <th>user</th>
        <th>creation datetime</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>

    <?php
    $uploadPath = \App\App::$uploadPath;
    ?>
    @foreach ($allRows as $row)
    <?php
    $user = $row->getUser();
    $user = $user->name." (".$user->id.")";

    $ext = pathinfo($row->filename, PATHINFO_EXTENSION);
    ?>
    <tr>
        <td>{$row->id}</td>
        <td>{$row->slug}</td>
        <td>{$row->filename}</td>
        <td>
            @if ($ext === "zip" || $ext === "pdf")
            <a href="{$uploadPath}{$row->filename}">{$row->filename}</a> <br>
            @else
            <img src="{$uploadPath}{$row->filename}" height="200px"> <br>
            @endif
        </td>
        <td>{$user}</td>
        <td>{$row->creation_datetime}</td>
        <td><a href="{queryString admin/medias/update/$row->id}">Edit</a></td>
        <td>
            <?php
            $form = new \App\Form("mediadelete".$row->id);
            $form->open($router->getQueryString("admin/medias/delete"));
            $form->hidden("id", $row->id);
            $form->submit("", "Delete");
            $form->close();
            ?>
        </td>
    </tr>
    @endforeach
</table>