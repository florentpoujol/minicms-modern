<?php

namespace App\Entities;

class Post extends BasePage
{
    public $category_id = -1;

    public function getLink(string $routeName = "post")
    {
        return parent::getLink($routeName);
    }
}
