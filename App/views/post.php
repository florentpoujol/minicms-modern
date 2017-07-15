
<article>
    <h1>{$post->title}</h1>

    <header>Posted by {$post->getUser()->name} in category {$post->getCategory()->title}</header>

    <p>
        <?= $post->content ?>
    </p>
</article>
