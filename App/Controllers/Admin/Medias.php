<?php

namespace App\Controllers\Admin;

use App\Config;
use App\Form;
use App\Lang;
use App\Renderer;
use App\Router;
use App\Session;
use App\Validator;
use App\Entities\Repositories\Media as MediaRepo;
use App\Entities\Repositories\User as UserRepo;

class Medias extends AdminBaseController
{
    /**
     * @var MediaRepo
     */
    protected $mediaRepo;

    /**
     * @var UserRepo
     */
    protected $userRepo;

    public function __construct(
        Lang $lang, Session $session, Validator $validator, Router $router, Renderer $renderer, Config $config, Form $form,
        MediaRepo $mediaRepo, UserRepo $userRepo)
    {
        parent::__construct($lang, $session, $validator, $router, $renderer, $config, $form);
        $this->mediaRepo = $mediaRepo;
        $this->userRepo = $userRepo;
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
        ];
        $this->render("medias.read", $data);
    }

    public function getCreate()
    {
        $this->render("medias.update", ["action" => "create"]);
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
            return;
        }

        $data = [
            "action" => "update",
            "post" => $media->toArray(),
            "users" => array_merge(
                $this->userRepo->getAll(["role" => "admin"]),
                $this->userRepo->getAll(["role" => "writer"])
            ),
        ];
        $this->render("medias.update", $data);
    }

    public function postUpdate(int $mediaId)
    {
        $post = $this->validator->sanitizePost([
            "slug" => "string",
            "user_id" => "int",
        ]);
        $post["id"] = $mediaId;

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
            "users" => array_merge(
                $this->userRepo->getAll(["role" => "admin"]),
                $this->userRepo->getAll(["role" => "writer"])
            ),
        ];
        $this->render("medias.update", $data);
    }

    public function postDelete(int $mediaId)
    {
        if ($this->validator->csrf("mediadelete$mediaId")) {
            $media = $this->mediaRepo->get($mediaId);
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
