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
        <?php
        if ($mainMenu !== false) {
            echo $mainMenu->buildStructure();
        } else {
            echo "No active menu";
        }
        ?>
    </nav>

    {viewContent}

</body>
</html>