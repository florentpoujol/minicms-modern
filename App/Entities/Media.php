<?php

namespace App\Entities;

use App\App;
use App\Messages;

class Media extends Entity
{
    public $slug = "";
    public $filename = "";
    public $user_id = -1;

     public function update(array $data): bool
    {
        unset($data["filename"]);
        return parent::update($data);
    }

    public function delete(): bool
    {
        $fileName = $this->filename;
        if (parent::delete()) {
            $path = __dir__ . "/../../public/" . App::$uploadPath . $fileName;
            if (file_exists($path)) {
                unlink($path);
            }
            return true;
        }
        return false;
    }

}