
<article>
    <h1>{$page->title}</h1>

    <p>
        <?= $page->content ?>
    </p>
</article>

<?php
$entity = $page;
$queryString = App\Route::buildQueryString("page/$page->id")
//  the include message is here because my "template system" does not support nested includes...
?>
{include ../App/views/messages.php}
{include ../App/views/comment.php}
