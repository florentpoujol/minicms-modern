<?php

namespace App\Controllers\Admin;

use App\Config;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;
use App\Entities\Repositories\Media as MediaRepo;

class Medias extends AdminBaseController
{
    /**
     * @var MediaRepo
     */
    public $mediaRepo;

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config,
        MediaRepo $mediaRepo)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config);
        $this->mediaRepo = $mediaRepo;
    }

    public function getRead(int $pageNumber = 1)
    {
        $allRows = $this->mediaRepo->getAll(["pageNumber" => $pageNumber]);

        $data = [
            "allRows" => $allRows,
            "pagination" => [
                "pageNumber" => $pageNumber,
                "itemsCount" => $this->mediaRepo->countAll(),
                "queryString" => $this->router->getQueryString("admin/medias/read")
            ],
            "pageTitle" => $this->lang->get("admin.media.readtitle"),
        ];
        $this->render("medias.read", $data);
    }

    public function getCreate()
    {
        $this->render("medias.update", [
            "action" => "create",
            "pageTitle" => $this->lang->get("admin.media.create"),
        ]);
    }

    public function postCreate()
    {
        $post = $this->validator->sanitizePost([
            "slug" => "string"
        ]);

        $post["user_id"] = $this->user->id;

        if ($this->validator->csrf("mediacreate")) {
            $ok = isset($_FILES["upload_file"]);
            if (! $ok) {
                $this->session->addError("fieldvalidation.file");
            }

            if ($this->validator->media($post) && $ok) {
                $media = $this->mediaRepo->create($post);

                if (is_object($media)) {
                    $this->session->addSuccess("media.created");
                    $this->router->redirect("admin/medias/update/$media->id");
                    return;
                } else {
                    $this->session->addError("media.create");
                }
            }
        } else {
            $this->session->addError("csrffail");
        }

        $data = [
            "action" => "create",
            "post" => $post,
            "pageTitle" => $this->lang->get("admin.media.create"),
        ];
        $this->render("medias.update", $data);
    }

    public function getUpdate(int $mediaId)
    {
        $media = $this->mediaRepo->get($mediaId);
        if ($media === false) {
            $this->session->addError("media.unknown");
            $this->router->redirect("admin/medias/read");
            return;
        }

        if ($this->user->isWriter() && $media->user_id !== $this->user->id) {
            $this->session->addError("user.notallowed");
            $this->router->redirect("admin/medias/read");
        }

        $data = [
            "action" => "update",
            "post" => $media->toArray(),
            "pageTitle" => $this->lang->get("admin.media.updatetitle"),
        ];
        $this->render("medias.update", $data);
    }

    public function postUpdate()
    {
        $post = $this->validator->sanitizePost([
            "id" => "int",
            "slug" => "string",
            "user_id" => "int"
        ]);

        if ($this->validator->csrf("mediaupdate")) {
            if ($this->validator->media($post)) {
                $media = $this->mediaRepo->get($post["id"]);

                if ($this->user->isWriter() && $media->user_id !== $this->user->id) {
                    $this->session->addError("user.notallowed");
                    $this->router->redirect("admin/medias/read");
                    return;
                }

                if (is_object($media)) {
                    if ($media->update($post)) {
                        $this->session->addSuccess("media.updated");
                        $this->router->redirect("admin/medias/update/$media->id");
                        return;
                    } else {
                        $this->session->addError("db.mediaupdated");
                    }
                } else {
                    $this->session->addError("media.unknown");
                }
            }
        } else {
            $this->session->addError("csrffail");
        }

        $media = $this->mediaRepo->get($post["id"]);
        $post["filename"] = $media->filename;
        $post["creation_datetime"] = $media->creation_datetime;

        $data = [
            "action" => "update",
            "post" => $post,
            "pageTitle" => $this->lang->get("admin.media.updatetitle"),
        ];
        $this->render("medias.update", $data);
    }

    public function postDelete()
    {
        $id = (int)$_POST["id"];
        if ($this->validator->csrf("mediadelete$id")) {
            $media = $this->mediaRepo->get($id);
            if (is_object($media)) {
                if ($media->delete()) {
                    $this->session->addSuccess("media.deleted");
                } else {
                    $this->session->addError("media.deleting");
                }
            } else {
                $this->session->addError("media.unknown");
            }
        } else {
            $this->session->addError("csrffail");
        }

        $this->router->redirect("admin/medias/read");
    }
}
