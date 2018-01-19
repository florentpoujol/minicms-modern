<?php

namespace App\Entities\Repositories;

use App\Entities\Page as PageEntity;
use App\Entities\Comment as CommentEntity;
use App\Entities\User as UserEntity;
use PDO;

class Page extends Entity
{
    /**
     * @var Comment
     */
    public $commentRepo;

    /**
     * @var User
     */
    public $userRepo;

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
     * @return PageEntity|bool|null
     */
    public function getParent(PageEntity $page)
    {
        if ($page->parent_page_id !== null) {
            return $this->get($page->parent_page_id);
        }
        return null;
    }

    /**
     * @return PageEntity[]|bool
     */
    public function getChildren(PageEntity $page)
    {
        $query = $this->database->getQueryBuilder()
            ->select()->fromTable("pages")
            ->where("parent_page_id", $page->id)
            ->execute();

        if ($query !== false) {
            $query->setFetchMode(PDO::FETCH_CLASS, PageEntity::class);
            return $query->fetchAll();
        }
        return false;
    }

    /**
     * @return UserEntity|bool
     */
    public function getUser(PageEntity $page)
    {
        return $this->userRepo->get($page->id);
    }

    /**
     * @return CommentEntity[]|bool
     */
    public function getComments(PageEntity $page)
    {
        return $this->commentRepo->getAll(["page_id" => $page->id]);
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
