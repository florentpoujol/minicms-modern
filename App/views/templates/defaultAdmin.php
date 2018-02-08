<!DOCTYPE html>
<html>
<head>
    <title>{$pageTitle}</title>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" type="text/css" href="<?= $config->get("siteDirectory"); ?>common.css">
    <link rel="stylesheet" type="text/css" href="<?= $config->get("siteDirectory"); ?>back.css">
</head>
<body>
    <nav id="main-menu">
        <ul>
            <li><a href="{queryString blog}">{config site_title}</a></li>

            @if ($user->isAdmin() || $user->isWriter())
            <li><a href="{queryString admin/categories}">{lang category}</a></li>
            <li><a href="{queryString admin/posts}">{lang post}</a></li>
            <li><a href="{queryString admin/pages}">{lang page}</a></li>
            @endif

            <li><a href="{queryString admin/comments}">{lang comment}</a></li>
            <li><a href="{queryString admin/users}">{lang user}</a></li>

            @if ($user->isAdmin() || $user->isWriter())
            <li><a href="{queryString admin/menus}">{lang menu}</a></li>
            <li><a href="{queryString admin/medias}">{lang media}</a></li>
            @endif

            @if ($user->isAdmin())
            <li><a href="{queryString admin/config}">{lang config}</a></li>
            @endif

            <li><a href="{queryString logout}">{lang logout}</a></li>
        </ul>
    </nav>

    <section id="main-content">
        {viewContent}
    </section>
</body>
</html>