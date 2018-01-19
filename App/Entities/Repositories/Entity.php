<?php

namespace App\Entities\Repositories;

use App\Config;
use App\Database;
use App\Entities\Entity as BaseEntity;
use PDO;

class Entity
{
    /**
     * @var Database
     */
    public $database;

    /**
     * @var Config
     */
    protected $config;

    protected $className = "";

    protected $tableName = "";

    public function __construct(Database $database, Config $config)
    {
        $this->database = $database;
        $this->config = $config;

        $this->className = str_replace("\Repositories", "", get_called_class());

        $entityName = str_replace("App\Entities\\", "", $this->className);

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

        if ($query !== false) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->className);
            return $query->fetch();
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
            $query->setFetchMode(PDO::FETCH_CLASS, $this->className);
            return $query->fetchAll();
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
            return (int)$query->fetch()->{"COUNT(*)"};
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

        $success = $this->database->getQueryBuilder()
            ->update($data)
            ->inTable($this->tableName)
            ->where("id", $entity->id)
            ->execute();

        if ($success) {
            foreach ($data as $field => $value) {
                $entity->{$field} = $value;
            }
        }
        return $success;
    }

    public function delete($entity): bool
    {
        $success = $this->database->getQueryBuilder()
            ->delete()
            ->fromTable($this->tableName)
            ->where("id", $entity->id)
            ->execute();

        if ($success) {
            $props = get_object_vars($entity);
            $entity->isDeleted = true;
            /*foreach ($props as $field => $value) {
                $entity->{$field} = null;
            }*/
        }
        return $success;
    }
}