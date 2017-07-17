<hr>
<h3>Comments</h3>
<?php
$allowComments = \App\Config::get("allow_comments");
if ($allowComments && $entity->allowComments()) {
    if (isset($this->user)) {
        if (! isset($commentPost)) {
            $commentPost = [];
        }
        $form = new App\Form("commentcreate".$this->user->id."_$entity->id", $commentPost);
        $form->open($queryString);
        $form->textarea("content", ["rows" => 5]);

        $strEntity = "post";
        if ($entity instanceof \App\Entities\Page) {
            $strEntity = "page";
        }
        $form->hidden($strEntity."_id", $entity->id);

        $form->submit("", "Submit a new comment");
        $form->close();
    }

    $comments = $entity->getComments();
    ?>

    <section>
        @foreach ($comments as $comment)
        <div>
            <p>Posted by {$comment->getUser()->name} on {$comment->creation_datetime}</p>
            <p>{$comment->content}</p>
        </div>
        @endforeach
    </section>

    <?php
}
?>