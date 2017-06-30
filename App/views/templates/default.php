<!DOCTYPE html>
<html>
<head>

    <title>{$pageTitle}</title>
    <meta charset="utf-8">

<?php
$robots = "noindex,nofollow";
?>
    <meta name="robots" content="<?php echo $robots; ?>">

    <link rel="stylesheet" type="text/css" href="<?php echo \App\App::$directory; ?>style.css">
</head>
<body>

    <nav id="main-menu">
        menu
    </nav>

    {viewContent}

</body>
</html>