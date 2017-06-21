<?php

namespace App\Entities;

/**
 * Class User
 * Instances represents the logged in user.
 * @package App\Entities
 */
class Page extends Entity
{
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
        return parent::_get($params, $condition, "pages", "Page");
    }

    /**
     * @param $params
     * @return Page[]|bool
     */
    public static function getAll($params)
    {
        return parent::_getAll($params, "pages", "Page");
    }

    public static function countAll()
    {
        return parent::_countAll("users");
    }

    /**
     * @return Comment[]|bool
     */
    public function getComments()
    {
        return Comment::getAll(["page_id" => $this->id]);
    }

    /**
     * @param array $newUser
     * @return Page|bool
     */
    public static function create($newPage)
    {

    }

    public function delete()
    {

    }

    public function getExcerpt()
    {

    }
}
