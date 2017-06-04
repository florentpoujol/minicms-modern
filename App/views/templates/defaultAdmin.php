<!DOCTYPE html>
<html>
<head>

    <title>{pageTitle}</title>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">

    <link rel="stylesheet" type="text/css" href="<?php echo \App\App::$directory; ?>common.css">
    <link rel="stylesheet" type="text/css" href="<?php echo \App\App::$directory; ?>backend.css">
</head>
<body>

<nav id="main-menu">
    <ul>
        <li><a href="{queryString admin/categories}">{lang categories.pagetitle}</a></li>
        <li><a href="{queryString admin/posts}">{lang posts.pagetitle}</a></li>
        <li><a href="{queryString admin/pages}">{lang pages.pagetitle}</a></li>
        <li><a href="{queryString admin/users}">{lang users.pagetitle}</a></li>
    </ul>
</nav>

<?php echo $content; ?>

</body>
</html>