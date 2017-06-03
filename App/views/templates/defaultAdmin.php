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
    admin menu
</nav>

<?php echo $content; ?>

</body>
</html>