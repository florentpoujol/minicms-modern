<?php

namespace App\Entities;

class BasePage extends Entity
{
    public $slug = "";
    public $title = "";
    public $content = "";
    public $published = -1;
    public $allow_comments = -1;
    public $user_id = -1;

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
