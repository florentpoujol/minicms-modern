<h2>{lang post.plural}</h2>

@if (count($posts) > 0)
    @foreach ($posts as $post)
        <article>
            <h3><?= $post->getLink() ?></h3>

            {include post-includes/header.php}

            <p>
                {$post->getExcerpt()}
            </p>
        </article>
    @endforeach
@else
    <p>{lang post.nopostyet}</p>
@endif