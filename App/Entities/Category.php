<?php

namespace App\Entities;

class Category extends Entity
{
    public $slug = "";

    public function getLink(string $routeName = "category")
    {
        return parent::getLink($routeName);
    }
}
