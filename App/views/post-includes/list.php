<section id="posts-list">
    @if (count($posts) > 0)
        @foreach ($posts as $post)
            <article class="post post-list">
                <h3><?= $post->getLink() ?></h3>

                {include post-includes/header.php}

                <p>
                    <?= $post->transformMarkdown(); ?>
                </p>
            </article>
        @endforeach
    @else
        <p>{lang post.nopostyet}</p>
    @endif
</section>