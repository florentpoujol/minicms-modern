<?php
$pageCount = ceil($pagination["itemsCount"] / $config->get("items_per_page"));
$pagination['queryString'] = rtrim($pagination['queryString'], "/")."/";
?>
<br>
@for ($i=1; $i<=$pageCount; $i++)
    @if ($i === $pagination["pageNumber"])
    <strong><a href="{$pagination['queryString']}{$i}">{$i}</a></strong>
    @else
    <a href="{$pagination['queryString']}{$i}">{$i}</a>
    @endif
@endfor
