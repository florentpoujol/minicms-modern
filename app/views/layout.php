<!DOCTYPE html>
<html>
<head>
    <title><?php echo $pageTitle ?></title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" type="text/css" href="style.css">

    <?php if ($headView !== "") require_once "$headView.php"; ?>
</head>
<body>
    <?php require_once "$bodyView.php"; ?>
</body>
</html>