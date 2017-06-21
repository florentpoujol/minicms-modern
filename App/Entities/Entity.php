<?php

namespace App\Entities;

use \PDO;

class Entity extends \App\Database
{
    public $id;
    public $creation_datetime;

    protected $table = "";
    protected $className = "";

    public function __construct()
    {
        $this->className = str_replace("App\Entities\\", "", get_called_class());
        $this->table = strtolower($this->className)."s";
    }

    /**
     * @param string $tableName The table name
     * @param array $params One or several WHERE clause from which to find the user. The keys must match the database fields names.
     * @param string $condition Should be AND or OR
     * @return \App\Entities\Entity|bool Entity populated from DB data or false on error
     */
    protected static function _get($params, $condition = "AND", $tableName, $className)
    {
        $strQuery = "SELECT * FROM $tableName WHERE ";
        foreach ($params as $name => $value) {
            $strQuery .= "$name=:$name $condition ";
        }
        $strQuery = rtrim($strQuery," $condition ");

        $query = self::$db->prepare($strQuery);
        $query->setFetchMode(PDO::FETCH_CLASS, "App\Entities\\$className");
        $success = $query->execute($params);

        if ($success) {
            return $query->fetch();
        }
        return false;
    }

    /**
     * @param array $params
     * @param string $tableName
     * @param string $className
     * @return array|bool
     */
    protected static function _getAll($params = [], $tableName, $className)
    {
        if (isset($params["pageNumber"])) {
            $pageNumber = $params["pageNumber"] - 1;
            unset($params["pageNumber"]);
            if ($pageNumber < 0) {
                $pageNumber = 0;
            }
            $itemsPerPage = \App\Config::get("items_per_page");

            $params["offset"] = $pageNumber * $itemsPerPage;
            $params["count"] = $itemsPerPage;
        }

        $limitQuery = "";
        if (isset($params["offset"])) {
            $limitQuery = " LIMIT ".$params["offset"].", ".$params["count"];
            unset($params["offset"]);
            unset($params["count"]);
        }

        $strQuery = "SELECT * FROM $tableName";
        if (count(array_keys($params)) >= 1) {
            $strQuery .= " WHERE ";
            foreach ($params as $name => $value) {
                $strQuery .= "$name=:$name AND ";
            }
            $strQuery = rtrim($strQuery, " AND ");
        }

        $query = self::$db->prepare($strQuery.$limitQuery);
        $query->setFetchMode(PDO::FETCH_CLASS, "App\Entities\\$className");
        $success = $query->execute($params);

        if ($success) {
            return $query->fetchAll();
        }
        return false;
    }

    /**
     * @param string $tableName
     * @return int|bool
     */
    protected static function _countAll($tableName)
    {
        $query = self::$db->prepare("SELECT COUNT(*) FROM $tableName");
        $success = $query->execute();
        if ($success) {
            return (int)$query->fetch()->{"COUNT(*)"};
        }
        return false;
    }

    public function update($data)
    {
        $strQuery = "UPDATE ".$this->table." SET ";
        foreach ($data as $name => $value) {
            $strQuery .= "$name = :$name, ";
        }
        $strQuery = trim($strQuery, ", ");

        $strQuery .= " WHERE id = :id";
        $data["id"] = $this->id;

        $query = self::$db->prepare($strQuery);
        $success = $query->execute($data);

        if ($success) {
            foreach ($data as $name => $value) {
                $this->{$name} = $value;
            }
            return true;
        }
        return false;
    }

    public function _delete()
    {
        $query = self::$db->prepare("DELETE FROM $this->table WHERE id = ?");
        $success = $query->execute([$this->id]);

        if ($success) {
            $props = get_object_vars($this);
            foreach ($props as $name => $value) {
                $this->{$name} = null;
            }
            return true;
        }
        return false;
    }

    public function toArray()
    {
        return (array)$this;
    }
}