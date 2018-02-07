
<h1>{$pageTitle}</h1>

{include messages.php}

<section id="categories">
    <h2>{lang category.plural}</h2>
    <ul>
    @if (count($categories) > 0)
        @foreach ($categories as $category)
        <li><?= $category->getLink() ?></li>
        @endforeach
    @else
        <li>{lang category.nocategoryyet}</li>
    @endif
    </ul>
</section>


{include post-includes/list.php}

{include pagination.php}
