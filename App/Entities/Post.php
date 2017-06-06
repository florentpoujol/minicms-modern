<?php

namespace App\Entities;

class Post extends Entity
{
    public static function get($params, $condition = "AND")
    {
        return parent::_get($params, $condition, "posts", "Post");
    }

    public static function getAll($params)
    {
        return parent::_getAll($params, "posts", "Post");
    }
}
