<?php

namespace App\Entities;

use App\App;

class Entity
{
    public $isDeleted = false;

    public $id = -1;
    public $title = ""; // not all entities have a title, but it is used below (in getLink())
    public $creation_datetime;

    public static function hydrate($data)
    {
        $entity = App::$container->make(static::class);
        foreach ($data as $field => $value) {
            $entity->{$field} = $value;
        }
        if (isset($entity->creation_datetime)) {
            $entity->creation_datetime = new \DateTime($entity->creation_datetime);
        }
        return $entity;
    }

    public function delete()
    {
        $this->isDeleted = true;
    }

    public function getLink(string $routeName)
    {
        return '<a href="' . \App\Route::getURL("$routeName/$this->id") . '">' . $this->title . '</a>';
    }

    public function toArray(): array
    {
        return (array)$this;
    }
}
