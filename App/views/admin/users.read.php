
<h1>{$pageTitle}</h1>

{include messages.php}

@if ($user->isAdmin())
<a href="{queryString admin/users/create}" class="button">{lang users.createlink}</a> <br>
@endif
<br>

<table>
    <tr>
        <th>id</th>
        <th>name</th>
        <th>email</th>
        <th>role</th>
        <th>creation date</th>

        @if ($user->isAdmin())
        <th>email token</th>
        <th>password token</th>
        <th>password change time</th>
        @endif

        <th>Edit</th>

        @if ($user->isAdmin())
        <th>Delete</th>
        @endif
    </tr>

    @foreach ($allRows as $row)
    <tr>
        <td>{$row->id}</td>
        <td>{$row->name}</td>
        <td>{$row->email}</td>
        <td>{$row->role}</td>
        <td>{$row->creation_datetime->format("Y-m-d")}</td>

        @if ($user->isAdmin())
        <td>{$row->email_token}</td>
        <td>{$row->password_token}</td>
        <td>{$row->password_change_time}</td>
        @endif

        @if ($user->isAdmin() || $user->id === $row->id)
        <td><a href="{queryString admin/users/update/$row->id}" class="button button-edit">Edit</a></td>
        @else
        <td></td>
        @endif

        @if ($user->isAdmin())
        <td>
        <?php
        $form->setup("userdelete$row->id");
        $form->open($router->getQueryString("admin/users/delete/$row->id"));
        $form->submit("", "Delete", ["class" => "button button-delete"]);
        $form->close();
        ?>
        </td>
        @endif
    </tr>
    @endforeach
</table>

{include pagination.php}
