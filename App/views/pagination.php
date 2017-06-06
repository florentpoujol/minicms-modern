<?php
$pageCount = ceil($pagination["itemsCount"] / \App\Config::get("items_per_page"));
?>
<br>
@for ($i=1; $i<=$pageCount; $i++)
    @if ($i === $pageNumber)
    <strong><a href="{pagination['queryString']}{i}">{i}</a></strong>
    @else
    <a href="{pagination['queryString']}{i}">{i}</a>
    @endif
@endfor
