<?php

namespace App\Entities;

use Michelf\Markdown;

class BasePage extends Entity
{
    public $slug = "";
    public $title = "";
    public $content = "";
    public $published = -1;
    public $allow_comments = -1;
    public $user_id = -1;

    public function getExcerpt(int $characterCount = 200): string
    {
        if ($characterCount <= 0) {
            $characterCount = 200;
        }
        return substr(Markdown::defaultTransform($this->content), 0, $characterCount);
    }

    public function transformMarkdown(): string
    {
        return Markdown::defaultTransform($this->content);
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
