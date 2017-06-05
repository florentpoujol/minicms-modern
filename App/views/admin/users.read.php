
<h1>{pageTitle}</h1>

<?php include __dir__."/../views/messages.php"; ?>

<a href="{@queryString admin/users/create}">{@lang users.createlink}</a> <br>
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
        <th>Edit</th>
        <th>Delete</th>
        @endif
    </tr>

    @foreach (allRows as $row)
    <tr>
        <td>{rows->id}</td>
        <td>{rows->name}</td>
        <td>{rows->email}</td>
        <td>{rows->role}</td>
        <td>{rows->creation_datetime}</td>

        @if ($user->isAdmin())
        <td>{row->email_token}</td>
        <td>{row->password_token}</td>
        <td>{row->password_change_time}</td>


        <td><a href="{@queryString admin/users/update/$row->id}">Edit</a></td>
        <td>
        <?php
        $form = new \App\Form("userdelete".$row->id);
        $form->open(\App\Route::buildQueryString("admin/users/delete"));
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
