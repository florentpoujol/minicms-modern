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
     * @return Comment[]|bool
     */
    public function getComments()
    {
        return [];
    }

    public function delete(): bool
    {
        $comments = $this->getComments();
        foreach ($comments as $comment) {
            $comment->delete();
        }

        return parent::delete();
    }

    public function getExcerpt(int $characterCount = 500): string
    {
        if ($characterCount <= 0) {
            $characterCount = 500;
        }
        return substr($this->content, 0, $characterCount);
    }

    public function isPublished(): bool
    {
        return $this->published === 1;
    }

    public function allowComments(): bool
    {
        return $this->allow_comments === 1;
    }
}
