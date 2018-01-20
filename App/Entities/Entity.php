<?php

namespace App\Entities;

use App\App;

class Entity
{
    public $isDeleted = false;

    public $id = -1;
    public $title = ""; // not all entities have a title, but it is used below (in getLink())
    public $creation_datetime;

    public static function createHydrated($data)
    {
        $entity = App::$container->make(static::class);
        $entity->hydrate($data);
        return $entity;
    }

    public function hydrate(array $data)
    {
        foreach ($data as $field => $value) {
            if ($field === "creation_datetime") {
                $value = new \DateTime($value);
            }
            $this->{$field} = $value;
        }
    }

    public function getEntityName(): string
    {
        $name = str_replace("App\Entities\\", "", get_called_class());
        return strtolower($name);
    }

    public function update(array $data)
    {
        $repoName = $this->getEntityName() . "Repo";
        if ($this->{$repoName}->update($this, $data)) {
            $this->hydrate($data);
            return true;
        }
        return false;
    }

    public function delete()
    {
        $repoName = $this->getEntityName() . "Repo";
        if ($this->{$repoName}->delete($this)) {
            $this->isDeleted = true;
            return true;
        }
        return false;
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
