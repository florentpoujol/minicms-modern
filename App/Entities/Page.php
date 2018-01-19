<?php

namespace App\Entities;

class Page extends BasePage
{
    public $parent_page_id = -1;

    public function getLink(string $routeName = "page")
    {
        return parent::getLink($routeName);
    }
}
