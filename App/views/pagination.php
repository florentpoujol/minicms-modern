<?php
$pageCount = ceil($paginationItemsCount / \App\Config::get("items_per_page"));
?>

@for ($i=1; $i<=$pageCount; $i++)
    @if ($i === $pageNumber)
    <strong><a href="{paginationTarget}{i}">{i}</a></strong>
    @else
    <a href="{paginationTarget}{i}">{i}</a>
    @endif
@endfor
