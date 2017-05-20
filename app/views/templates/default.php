<!DOCTYPE html>
<html>
<head>

    <title><?php echo $pageTitle ?></title>
    <meta charset="utf-8">

<?php
$robots = "noindex,nofollow";
?>
    <meta name="robots" content="<?php echo $robots; ?>">

    <link rel="stylesheet" type="text/css" href="<?php echo \App\App::$directory; ?>common.css">
    <link rel="stylesheet" type="text/css" href="<?php echo \App\App::$directory; ?>frontend.css">
</head>
<body>

    <nav id="main-menu">
        menu
    </nav>

    <?php echo $content; ?>

</body>
</html>