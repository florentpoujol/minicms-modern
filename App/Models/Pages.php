<?php

namespace App\Models;

class Pages extends Model
{
    public static function get($params, $condition = "AND")
    {
        return parent::getFromTable("pages", $params, $condition);
    }

    public static function insert($newPage)
    {
        //
        return false;
    }


}
