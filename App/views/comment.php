<?php
$allowComments = $config->get("allow_comments");
if ($allowComments && $entity->allowComments()) {
?>
<hr>
<h3>{lang comment.plural}</h3>
<?php
    if (isset($user)) {
        $form->setup(
                "comment_create_" . $user->id . "_" . $entity->id,
                $commentPost ?? []
        );
        $form->open($queryString); // set in post and page views
        $form->textarea("content", ["rows" => 7, "cols" => 50]);

        $form->submit("", $lang->get("comment.submit"), ["class" => "button"]);
        $form->close();
    } else {
?>
        <p><a href="{queryString login}">Login</a> or <a href="{queryString register}">Register</a> to add a new comment.</p>
<?php
    }

    $comments = $entity->getComments();
    $commentCount = count($comments);
    ?>
    @if ($commentCount > 0)
        @foreach ($comments as $comment)
        <article>
            <p>Posted by {$comment->getUser()->name} on <?= $comment->creation_datetime->format("Y-m-d H:i:s"); ?></p>
            <p>{$comment->content}</p>
        </article>
        @endforeach
    @else
        No comment yet.
    @endif

    <?php
}
?>