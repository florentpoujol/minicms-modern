
<h1>{pageTitle}</h1>

{include ../App/views/messages.php}

@if ($this->user->isAdmin())
<a href="{@queryString admin/users/create}">{@lang users.createlink}</a> <br>
@endif
<br>

<table>
    <tr>
        <th>id</th>
        <th>name</th>
        <th>email</th>
        <th>role</th>
        <th>creation date</th>

        @if ($this->user->isAdmin())
        <th>email token</th>
        <th>password token</th>
        <th>password change time</th>
        <th>Edit</th>
        <th>Delete</th>
        @endif
    </tr>

    @foreach ($allRows as $row)
    <tr>
        <td>{row->id}</td>
        <td>{row->name}</td>
        <td>{row->email}</td>
        <td>{row->role}</td>
        <td>{row->creation_datetime}</td>

        @if ($this->user->isAdmin())
        <td>{row->email_token}</td>
        <td>{row->password_token}</td>
        <td>{row->password_change_time}</td>
        @endif

        @if ($this->user->isAdmin() || $this->user->id === $row->id)
        <td><a href="{@queryString admin/users/update/$row->id}">Edit</a></td>
        @endif

        @if ($this->user->isAdmin())
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
