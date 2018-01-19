<?php

namespace App\Entities;

class Entity
{
    public $id = -1;
    public $title = ""; // not all entities have a title, but it is used below (in getLink())
    public $creation_datetime;

    public function getLink(string $routeName)
    {
        return '<a href="' . \App\Route::getURL("$routeName/$this->id") . '">' . $this->title . '</a>';
    }

    public function toArray(): array
    {
        return (array)$this;
    }
}
