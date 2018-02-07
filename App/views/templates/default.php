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
        if ($mainMenu instanceof \App\Entities\Menu) {
            echo $mainMenu->buildStructure();
        } elseif ($mainMenu === false) {
            echo "No active menu";
        }
        // else $mainMenu === "install" > don't display anything
        ?>
    </nav>

    {viewContent}

</body>
</html>