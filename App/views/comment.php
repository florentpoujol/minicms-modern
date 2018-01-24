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
        $form->textarea("content", ["rows" => 5]);

        /*$strEntity = "post";
        if ($entity instanceof \App\Entities\Page) {
            $strEntity = "page";
        }
        $form->hidden($strEntity . "_id", $entity->id);*/

        $form->submit("", $lang->get("comment.submit"));
        $form->close();
    }

    $comments = $entity->getComments();
    ?>

    <article>
        @foreach ($comments as $comment)
        <div>
            <p>Posted by {$comment->getUser()->name} on {$comment->creation_datetime->format("Y-m-d H:i:s")}</p>
            <p>{$comment->content}</p>
        </div>
        @endforeach
    </article>

    <?php
}
?>