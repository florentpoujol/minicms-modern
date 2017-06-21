<?php

namespace App\Entities;

class Comment extends Entity
{
    public static function get($params, $condition = "AND")
    {
        return parent::_get($params, $condition, "comments", "Comment");
    }

    public static function getAll($params = [])
    {
        return parent::_getAll($params, "comments", "Comment");
    }

    public static function countAll()
    {
        return parent::_countAll("comments");
    }

    public function delete()
    {
        return self::_delete();
    }
}
