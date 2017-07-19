
<article>
    <h1>{$post->title}</h1>

    <header>Posted by {$post->getUser()->name} in category {$post->getCategory()->title}</header>

    <p>
        <?= $post->content ?>
    </p>
</article>

<?php
$entity = $post;
$queryString = App\Route::buildQueryString("post/$post->id")
//  the include message is here because my "template system" does not support nested includes...
?>
{include ../App/views/messages.php}
{include ../App/views/comment.php}