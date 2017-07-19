<?php

namespace App\Entities;

use App\App;
use App\Messages;

class Media extends Entity
{
    public $slug;
    public $filename;
    public $user_id;

    /**
     * @param string $fileSlug
     * @return bool|string
     */
    public static function upload($fileSlug)
    {
        $uploadPath = \App\App::$uploadPath;
        if (! is_writable($uploadPath)) {
            Messages::addError("file.uploadfoldernotwritable");
            return false;
        }

        $file = $_FILES["upload_file"];
        $tmpName = $file["tmp_name"];
        $fileName = basename($file["name"]);

        // Check extension
        $allowedExtensions = ["jpg", "jpeg", "png", "pdf", "zip"];
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $hasValidExtension = in_array($extension, $allowedExtensions);

        // check actual MIME Type
        $allowedMimeTypes = ["image/jpeg", "image/png", "application/pdf", "application/zip"];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpName);
        $hasValidMimeType = in_array($mimeType, $allowedMimeTypes);

        if ($hasValidExtension && $hasValidMimeType) {
            $creationDate = date("Y-m-d");
            $fileName = str_replace(" ", "-", $fileName);
            // add the creation date between the slug of the file and the extension
            $fileName = preg_replace("/(\.[a-zA-Z]{3,4})$/i", "-$fileSlug-$creationDate$1", $fileName);

            if (move_uploaded_file($tmpName, __dir__ . "/../../public/$uploadPath/$fileName")) {
                return $fileName;
            } else {
                Messages::addError("file.errormovinguploadedfile");
            }
        } else {
            Messages::addError("file.wrongextensionormimetype");
        }
        return false;
    }

    public static function create($data)
    {
        $fileName = self::upload($data["slug"]);
        if ($fileName !== false) {
            $data["filename"] = $fileName;
            return parent::create($data);
        }
        return false;
    }

    public function update($data)
    {
        unset($data["filename"]);
        return parent::update($data);
    }

    public function delete()
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

    /**
     * @return User|false
     */
    public function getUser()
    {
        return User::get($this->user_id);
    }
}