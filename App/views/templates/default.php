<!DOCTYPE html>
<html>
<head>
    <title>{$pageTitle}</title>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" type="text/css" href="<?= $config->get("siteDirectory"); ?>common.css">
    <link rel="stylesheet" type="text/css" href="<?= $config->get("siteDirectory"); ?>front.css">
</head>
<body>

    <nav id="main-menu">
        <section id="site-title"><p>{config site_title}</p></section>
        <?php
        if ($mainMenu instanceof \App\Entities\Menu) {
            echo $mainMenu->buildStructure();
        } elseif ($mainMenu === false) {
            echo "No active menu";
        }
        // else $mainMenu === "install" > don't display anything
        ?>
    </nav>

    <div id="main-content">
        {viewContent}
    </div>
</body>
</html>