
<h1>{$pageTitle}</h1>

{include ../App/views/messages.php}

<div>
    <h2>Posts</h2>
    @if (count($posts) > 0)
        @foreach ($posts as $post)
        <article>
            <h3><?= $post->getLink() ?></h3>
            <header>Created by {$post->getUser()->name} in category <?= $category->title ?></header>
            <p>
                {$post->getExcerpt()}
            </p>
        </article>
        @endforeach
    @else
        <p>No post yet in this category</p>
    @endif
</div>

{include ../App/views/pagination.php}
