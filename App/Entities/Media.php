<?php

namespace App\Entities;

use App\Config;
use App\Entities\Repositories\Media as MediaRepo;
use App\Entities\Repositories\User as UserRepo;
use App\Router;

class Media extends Entity
{
    use UserOwnedTrait;

    public $slug = "";
    public $filename = "";
    public $user_id = -1;

    /**
     * @var MediaRepo
     */
    protected $mediaRepo;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(Router $router, MediaRepo $mediaRepo, UserRepo $userRepo, Config $config)
    {
        parent::__construct($router);
        $this->userRepo = $userRepo;
        $this->mediaRepo = $mediaRepo;
        $this->config = $config;
    }

    public function delete(): bool
    {
        if (parent::delete()) {
            $path = $this->config->get("upload_path") . $this->filename;
            if (file_exists($path)) {
                unlink($path);
            }
            return true;
        }
        return false;
    }
}
