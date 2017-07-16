
<h1>{$pageTitle}</h1>

{include ../App/views/messages.php}

<table>
    <tr>
        <th>id</th>
        <th>user</th>
        <th>post</th>
        <th>page</th>
        <th>created at</th>
        <th>content</th>
        <th>Edit</th>
        @if ($this->user->isAdmin())
        <th>Delete</th>
        @endif
    </tr>

    @foreach ($allRows as $row)
    <?php
    $user = \App\Entities\User::get($row->user_id);
    if (is_object($user)) {
        $user = "$user->name ($user->id)";
    } else {
        $user = "";
    }

    $post = \App\Entities\Post::get($row->post_id);
    if (is_object($post)) {
        $post = "$post->title ($post->id)";
    } else {
        $post = "";
    }

    $page = \App\Entities\Page::get($row->page_id);
    if (is_object($page)) {
        $page = "$page->title ($page->id)";
    } else {
        $page = "";
    }
    ?>
    <tr>
        <td>{$row->id}</td>
        <td>{$user}</td>
        <td>{$post}</td>
        <td>{$page}</td>
        <td>{$row->creation_datetime}</td>
        <td>{$row->content}</td>

        <td><a href="{queryString admin/comments/update/$row->id}">Edit</a></td>
        @if ($this->user->isAdmin())
        <td>
            <?php
            $form = new \App\Form("commentdelete".$row->id);
            $form->open(\App\Route::buildQueryString("admin/comments/delete"));
            $form->hidden("id", $row->id);
            $form->submit("", "Delete");
            $form->close();
            ?>
        </td>
        @endif
    </tr>
    @endforeach
</table>

{include ../App/views/pagination.php}
