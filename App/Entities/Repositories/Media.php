<?php

namespace App\Entities\Repositories;

use App\Entities\Media as MediaEntity;

class Media extends Entity
{
    /**
     * @return MediaEntity|false
     */
    public function get($whereConditions, bool $useWhereOrOperator = false)
    {
        return parent::get($whereConditions, $useWhereOrOperator);
    }

    /**
     * @return MediaEntity[]|false
     */
    public function getAll(array $params = [])
    {
        return parent::getAll($params);
    }

    /**
     * @return MediaEntity|false
     */
    public function create(array $data)
    {
        $fileName = $this->upload($data["slug"]);
        if ($fileName !== false) {
            $data["filename"] = $fileName;
            return parent::create($data);
        }
        return false;
    }

    /**
     * @return string|bool
     */
    public function upload(string $fileSlug)
    {
        $uploadPath = $this->config->get("upload_path") . "/";
        if (!is_writable($uploadPath)) {
            $this->session->addError("file.uploadfoldernotwritable");
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

            if (move_uploaded_file($tmpName, $uploadPath . $fileName)) {
                return $fileName;
            } else {
                $this->session->addError("file.errormovinguploadedfile");
            }
        } else {
            $this->session->addError("file.wrongextensionormimetype");
        }

        return false;
    }

    /**
     * @param MediaEntity $media
     */
    public function update($media, array $data): bool
    {
        unset($data["filename"]);
        return parent::update($media, $data);
    }

    /**
     * @param MediaEntity $media
     */
    public function delete($media): bool
    {
        $fileName = $media->filename;
        if (parent::delete($media)) {
            $path = $this->config->get("upload_path") . $fileName;
            if (file_exists($path)) {
                unlink($path);
            }
            return true;
        }
        return false;
    }
}
