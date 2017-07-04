<?php

namespace App\Entities;


class BasePage extends Entity
{
    // fields from DB
    public $slug;
    public $title;
    public $content;
    public $published;
    public $allow_comments;

    /**
     * Must be overridden in child classes
     * @return array Empty array
     */
    public function getComments()
    {
        return [];
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $comments = $this->getComments();
        foreach ($comments as $comment) {
            $comment->delete();
        }

        return parent::delete();
    }

    /**
     * @param int $charSize
     * @return bool|string
     */
    public function getExcerpt($charSize = 500)
    {
        if ($charSize <= 0) {
            $charSize = 500;
        }
        return substr($this->content, 0, $charSize);
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return ($this->published === 1);
    }

    /**
     * @return bool
     */
    public function allowComments()
    {
        return ($this->allow_comments === 1);
    }
}
