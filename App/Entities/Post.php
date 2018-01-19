<?php

namespace App\Entities;

class Post extends BasePage
{
    public $user_id;
    public $category_id;

    public function getLink(string $routeName = "post")
    {
        return parent::getLink($routeName);
    }
}
