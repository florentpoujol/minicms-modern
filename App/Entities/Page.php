<?php

namespace App\Entities;

/**
 * Class User
 * Instances represents the logged in user.
 * @package App\Entities
 */
class Page extends Entity
{
    public $id;
    public $slug;
    public $title;
    public $content;
    public $parent_page_id;
    public $user_id;
    public $creation_datetime;
    public $published;
    public $allow_comment;

    /**
     * @param array $params
     * @param string $condition
     * @return Page|bool
     */
    public static function get($params, $condition = "AND")
    {
        return parent::getFromTable("pages", "Page", $params, $condition);
    }

    /**
     * @param array $newUser
     * @return Page|bool
     */
    public static function insert($newPage)
    {

    }

    public function update()
    {

    }

    public function delete()
    {

    }

    public function getExcerpt()
    {

    }
}
