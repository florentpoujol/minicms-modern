
<article>
    <h1>{$page->title}</h1>

    <p>
        <?= $page->transformMarkdown(); ?>
    </p>
</article>

<?php
$entity = $page;
$queryString = $router->getQueryString("page/$page->id")
//  the include message is here because my "template system" does not support nested includes...
?>
{include messages.php}
{include comment.php}
