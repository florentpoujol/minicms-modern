<?php

namespace App\Entities\Repositories;

use App\Config;
use App\Database;
use App\Entities\Page as PageEntity;
use App\Session;

class Page extends Entity
{
    /**
     * @var Comment
     */
    protected $commentRepo;

    public function __construct(Database $database, Config $config, Session $session, Comment $commentRepo)
    {
        parent::__construct($database, $config, $session);
        $this->commentRepo = $commentRepo;
    }

    /**
     * @return PageEntity|false
     */
    public function get($whereConditions, bool $useWhereOrOperator = false)
    {
        return parent::get($whereConditions, $useWhereOrOperator);
    }

    /**
     * @return PageEntity[]|false
     */
    public function getAll(array $params = [])
    {
        return parent::getAll($params);
    }

    /**
     * @return PageEntity|false
     */
    public function create(array $data)
    {
        return parent::create($data);
    }

    /**
     * @param PageEntity $page
     */
    public function update($page, array $data): bool
    {
        if (isset($data["parent_page_id"]) && $data["parent_page_id"] <= 0) {
            $data["parent_page_id"] = null;
        }
        return parent::update($page, $data);
    }

    /**
     * @param PageEntity $page
     */
    public function delete($page): bool
    {
        if (parent::delete($page)) {
            // deparent children
            $this->database->getQueryBuilder()
                ->update(["parent_page_id" => null])
                ->inTable("pages")
                ->where("parent_page_id", $page->id)
                ->execute();

            $this->commentRepo->deleteMany(["page_id" => $page->id]);
            return true;
        }
        return false;
    }
}
