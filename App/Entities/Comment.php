<?php

namespace App\Entities;

class Comment extends Entity
{
    public $content = "";
    public $user_id = -1;
    public $post_id = -1;
    public $page_id = -1;
}
