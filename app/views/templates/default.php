<?php

namespace App;

?>
<!DOCTYPE html>
<html>
<head>

    <title>{title}</title>
    <meta charset="utf-8">

<?php
$robots = "noindex,nofollow";
/*if (isset($currentPage["published"]) && $currentPage["published"] === 1) {
    $robots = "index,follow";
}*/
?>
    <meta name="robots" content="<?php echo $robots; ?>">

    <link rel="stylesheet" type="text/css" href="{App::$siteDirectory}common.css">
    <link rel="stylesheet" type="text/css" href="{App::$siteDirectory}frontend.css">
</head>
<body>

    <nav id="main-menu">
        menu
    </nav>

    content :
    <?php echo $content; ?>

</body>
</html>