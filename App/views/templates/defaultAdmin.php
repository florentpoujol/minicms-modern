<!DOCTYPE html>
<html>
<head>
    <title>{$pageTitle}</title>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" type="text/css" href="<?= $config->get("siteDirectory"); ?>common.css">
</head>
<body>
    <nav id="main-menu">
        <ul>
            @if ($user->isAdmin())
            <li><a href="{queryString admin/config}">{lang config.title}</a></li>
            @endif
            @if ($user->isAdmin() || $user->isWriter())
            <li><a href="{queryString admin/categories}">{lang category.title}</a></li>
            <li><a href="{queryString admin/posts}">{lang post.title}</a></li>
            <li><a href="{queryString admin/pages}">{lang page.title}</a></li>
            <li><a href="{queryString admin/menus}">{lang menu.title}</a></li>
            <li><a href="{queryString admin/medias}">{lang media.title}</a></li>
            @endif
            <li><a href="{queryString admin/comments}">{lang comment.title}</a></li>
            <li><a href="{queryString admin/users}">{lang user.title}</a></li>
            <li><a href="{queryString logout}">{lang logout}</a></li>
            <li><a href="{queryString blog}">{config site_title}</a></li>
        </ul>
    </nav>

    {viewContent}
</body>
</html>