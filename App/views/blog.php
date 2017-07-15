
<h1>Blog</h1>

{include ../App/views/messages.php}

<div id="categories">
    <h2>Categories</h2>
    <ul>
    @if (count($categories) > 0)
        @foreach ($categories as $cat)
        <li><?= $cat->getLink() ?></li>
        @endforeach
    @else
        <li>No categories yet</li>
    @endif
    </ul>
</div>

<div>
    <h2>Posts</h2>
    @if (count($posts) > 0)
        @foreach ($posts as $post)
            <article>
                <h3><?= $post->getLink() ?></h3>
                <header>Created by {$post->getUser()->name} in category <?= $post->getCategory()->getLink() ?></header>
                <p>
                    {$post->getExcerpt()}
                </p>
            </article>
        @endforeach
    @else
        <p>No post yet</p>
    @endif
</div>

{include ../App/views/pagination.php}
