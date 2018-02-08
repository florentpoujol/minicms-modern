
<article class="post post-single">
    <h1>{$post->title}</h1>

    {include post-includes/header.php}

    <p>
        <?= $post->transformMarkdown(); ?>
    </p>
</article>

<?php
$entity = $post;
$queryString = $router->getQueryString("post/$post->id");
//  the include message is here because my "template system" does not support nested includes...
?>
{include messages.php}
{include comment.php}
