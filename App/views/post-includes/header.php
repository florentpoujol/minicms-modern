<header><?=
    $lang->get("post.createdbyheader", [
        "userName" => $post->getUser()->name,
        "categoryName" => $post->getCategory()->getLink(),
    ]); ?>
</header>
