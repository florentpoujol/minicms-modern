<!DOCTYPE html>
<html>
<head>
    <title>{$pageTitle}</title>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" type="text/css" href="<?= $config->get("siteDirectory"); ?>style.css">
</head>
<body>
    <nav id="main-menu">
        <ul>
            @if ($this->user->isAdmin())
            <li><a href="{queryString admin/config}">{lang config.title}</a></li>
            @endif
            @if (! $this->user->isCommenter())
            <li><a href="{queryString admin/menus}">{lang menu.title}</a></li>
            <li><a href="{queryString admin/medias}">{lang media.title}</a></li>
            <li><a href="{queryString admin/categories}">{lang category.title}</a></li>
            <li><a href="{queryString admin/posts}">{lang post.title}</a></li>
            <li><a href="{queryString admin/pages}">{lang page.title}</a></li>
            @endif
            <li><a href="{queryString admin/comments}">{lang comment.title}</a></li>
            <li><a href="{queryString admin/users}">{lang user.title}</a></li>
            <li><a href="{queryString logout}">{lang logout}</a></li>
        </ul>
    </nav>

    {viewContent}
</body>
</html>