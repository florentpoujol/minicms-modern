
<h1>{$pageTitle}</h1>

{include messages.php}

<table>
    <tr>
        <th>id</th>
        <th>user</th>
        <th>post</th>
        <th>page</th>
        <th>created at</th>
        <th>content</th>
        <th>Edit</th>
        @if ($user->isAdmin())
        <th>Delete</th>
        @endif
    </tr>

    @foreach ($allRows as $row)
    <?php
    $_user = $row->getUser();
    if (is_object($_user)) {
        $_user = "$_user->name ($_user->id)";
    } else {
        $_user = "";
    }

    $post = $row->getPost();
    if (is_object($post)) {
        $post = "$post->title ($post->id)";
    } else {
        $post = "";
    }

    $page = $row->getPage();
    if (is_object($page)) {
        $page = "$page->title ($page->id)";
    } else {
        $page = "";
    }
    ?>
    <tr>
        <td>{$row->id}</td>
        <td>{$_user}</td>
        <td>{$post}</td>
        <td>{$page}</td>
        <td>{$row->creation_datetime->format("Y-m-d")}</td>
        <td>{$row->getExcerpt()}</td>
        <td><a href="{queryString admin/comments/update/$row->id}" class="button button-edit">Edit</a></td>
        @if ($user->isAdmin())
        <td>
            <?php
            $form->setup("commentdelete$row->id");
            $form->open($router->getQueryString("admin/comments/delete/$row->id"));
            $form->submit("", "Delete", ["class" => "button button-delete"]);
            $form->close();
            ?>
        </td>
        @endif
    </tr>
    @endforeach
</table>

{include pagination.php}
