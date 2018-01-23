<?php

namespace App\Entities\Repositories;

use App\Config;
use App\Database;
use App\Session;
use App\Entities\Entity as BaseEntity;

class Entity
{
    /**
     * @var Database
     */
    protected $database;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Session
     */
    protected $session;

    protected $entityClassName = "";
    protected $tableName = "";

    public function __construct(Database $database, Config $config, Session $session)
    {
        $this->database = $database;
        $this->config = $config;
        $this->session = $session;

        $this->entityClassName = str_replace("\Repositories", "", get_called_class());
        $entityName = str_replace("App\Entities\\", "", $this->entityClassName);
        $this->tableName = strtolower($entityName) . "s";
    }

    /**
     * @param array|int $whereConditions One or several WHERE clause from which to find the user. The keys must match the database fields names.
     * @return BaseEntity|false Entity populated from DB data, or false on error or if nothing is found
     */
    public function get($whereConditions, bool $useWhereOrOperator = false)
    {
        if (!is_array($whereConditions)) {
            $whereConditions = ["id" => $whereConditions];
        }
        $whereFunction = $useWhereOrOperator ? "orWhere" : "where";

        $query = $this->database->getQueryBuilder()
            ->select()
            ->fromTable($this->tableName)
            ->{$whereFunction}($whereConditions)
            ->execute();

        if ($query !== false && ($result = $query->fetch()) !== false) {
            return $this->entityClassName::createHydrated($result);
        }
        return false;
    }

    /**
     * @return BaseEntity[]|false
     */
    public function getAll(array $params = [])
    {
        if (isset($params["pageNumber"])) {
            $pageNumber = $params["pageNumber"] - 1;
            unset($params["pageNumber"]);
            if ($pageNumber < 0) {
                $pageNumber = 0;
            }

            $itemsPerPage = $this->config->get("items_per_page");
            $params["offset"] = $pageNumber * $itemsPerPage;
            $params["count"] = $itemsPerPage;
        }

        $builder = $this->database->getQueryBuilder();
        if (isset($params["offset"])) {
            $builder->limit($params["count"])->offset($params["offset"]);
            unset($params["offset"]);
            unset($params["count"]);
        }

        $query = $builder->select()
            ->fromTable($this->tableName)
            ->where($params)
            ->orderBy("id")
            // LIMIT set above
            ->execute();

        if ($query !== false) {
            $results = [];
            foreach ($query->fetchAll() as $result) {
                $results[] = $this->entityClassName::createHydrated($result);
            }
            return $results;
        }
        return false;
    }

    /**
     * @return int|false
     */
    public function countAll(array $whereConditions = [])
    {
        $query = $this->database->getQueryBuilder()
            ->select("COUNT(*)")
            ->fromTable($this->tableName)
            ->where($whereConditions)
            ->execute();

        if ($query !== false) {
            return (int)$query->fetch()["COUNT(*)"];
        }
        return false;
    }

    /**
     * @return Entity|false
     */
    public function create(array $data)
    {
        unset($data["id"]);
        $data["creation_datetime"] = date("Y-m-d H:i:s");

        $lastInsertId = $this->database->getQueryBuilder()
            ->insert($data)
            ->inTable($this->tableName)
            ->execute();

        if ($lastInsertId !== false) {
            return $this->get(["id" => $lastInsertId]);
        }
        return false;
    }

    public function update($entity, array $data): bool
    {
        unset($data["id"]);

        return $this->database->getQueryBuilder()
            ->update($data)
            ->inTable($this->tableName)
            ->where("id", $entity->id)
            ->execute();
    }

    public function updateMany(array $newData, array $whereConditions): bool
    {
        $builder = $this->database->getQueryBuilder()
            ->update($newData)
            ->inTable($this->tableName);

        foreach ($whereConditions as $field => $value) {
            $builder->where($field, $value);
        }

        return $builder->execute();
    }

    public function delete($entity): bool
    {
        return $this->database->getQueryBuilder()
            ->delete()
            ->fromTable($this->tableName)
            ->where("id", $entity->id)
            ->execute();
    }

    public function deleteMany(array $whereConditions): bool
    {
        return $this->database->getQueryBuilder()
            ->delete()->fromTable($this->tableName)
            ->where($whereConditions)
            ->execute();
    }
}