<?php

namespace App\Controllers\Admin;

use App\Entities\Media;
use App\Messages;
use App\Route;
use App\Validate;

class Medias extends AdminBaseController
{
    public function getRead($pageNumber = 1)
    {
        $allRows = Media::getAll(["pageNumber" => $pageNumber]);

        $data = [
            "allRows" => $allRows,
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => Media::countAll(),
                "queryString" => Route::buildQueryString("admin/medias/read")
            ]
        ];
        $this->render("medias.read", "admin.media.readtitle", $data);
    }

    public function getCreate()
    {
        $this->render("medias.update", "admin.media.create", ["action" => "create"]);
    }

    public function postCreate()
    {
        $post = Validate::sanitizePost([
            "slug" => "string"
        ]);

        $post["user_id"] = $this->user->id;

        if (Validate::csrf("mediacreate")) {
            $ok = isset($_FILES["upload_file"]);
            if ($ok === false) {
                Messages::addError("fieldvalidation.file");
            }
            if (Validate::media($post) && $ok) {
                $media = Media::create($post);

                if (is_object($media)) {
                    Messages::addSuccess("media.created");
                    Route::redirect("admin/medias/update/".$media->id);
                } else {
                    Messages::addError("media.create");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        $data = [
            "action" => "create",
            "post" => $post
        ];
        $this->render("medias.update", "admin.media.create", $data);
    }

    public function getUpdate($id)
    {
        $media = Media::get($id);
        if ($media === false) {
            Messages::addError("media.unknown");
            Route::redirect("admin/medias/read");
        }

        if ($this->user->isWriter() && $media->user_id !== $this->user->id) {
            Messages::addError("user.notallowed");
            Route::redirect("admin/medias/read");
        }

        $data = [
            "action" => "update",
            "post" => $media->toArray()
        ];
        $this->render("medias.update", "admin.media.updatetitle", $data);
    }

    public function postUpdate()
    {
        $post = Validate::sanitizePost([
            "id" => "int",
            "slug" => "string",
            "user_id" => "int"
        ]);

        if (Validate::csrf("mediaupdate")) {

            if (Validate::media($post)) {
                $media = Media::get($post["id"]);

                if ($this->user->isWriter() && $media->user_id !== $this->user->id) {
                    Messages::addError("user.notallowed");
                    Route::redirect("admin/medias/read");
                }

                if (is_object($media)) {
                    if ($media->update($post)) {
                        Messages::addSuccess("media.updated");
                        Route::redirect("admin/medias/update/".$media->id);
                    } else {
                        Messages::addError("db.mediaupdated");
                    }
                } else {
                    Messages::addError("media.unknown");
                }
            }
        } else {
            Messages::addError("csrffail");
        }

        $media = Media::get($post["id"]);
        $post["filename"] = $media->filename;
        $post["creation_datetime"] = $media->creation_datetime;

        $data = [
            "action" => "update",
            "post" => $post
        ];
        $this->render("medias.update", "admin.media.updatetitle", $data);
    }

    public function postDelete()
    {
        $id = (int)$_POST["id"];
        if (Validate::csrf("mediadelete$id")) {
            $media = Media::get($id);
            if (is_object($media)) {
                if ($media->delete()) {
                    Messages::addSuccess("media.deleted");
                } else {
                    Messages::addError("media.deleting");
                }
            } else {
                Messages::addError("media.unknown");
            }
        } else {
            Messages::addError("csrffail");
        }

        Route::redirect("admin/medias/read");
    }
}
