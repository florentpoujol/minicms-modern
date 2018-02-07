<?php
$pageCount = ceil($pagination["itemsCount"] / $config->get("items_per_page"));
$pagination['queryString'] = rtrim($pagination['queryString'], "/")."/";
?>
<section class="pagination">
    @for ($i=1; $i<=$pageCount; $i++)
        <?php
        $current = $i === $pagination["pageNumber"] ? 'class="current"' : "";
        ?>
        <a href="{$pagination['queryString']}{$i}" <?= $current; ?>>{$i}</a>
    @endfor
</section>