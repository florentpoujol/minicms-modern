<?php

namespace App\Entities;

use \PDO;

class Entity extends \App\Database
{
    public $id;
    public $creation_datetime;

    /**
     * @return string
     */
    protected static function getTableName()
    {
        $tableName = str_replace("App\Entities\\", "", get_called_class());
        return strtolower($tableName."s");
    }

    /**
     * @param array|int $params One or several WHERE clause from which to find the user. The keys must match the database fields names.
     * @param string $condition Should be AND or OR
     * @return $this|bool Entity populated from DB data, or false on error or if nothing is found
     */
    public static function get($params, $condition = "AND")
    {
        if (is_int($params)) {
            $params = ["id" => $params];
        }

        $strQuery = "SELECT * FROM ".self::getTableName()." WHERE ";
        foreach ($params as $name => $value) {
            $strQuery .= "$name=:$name $condition ";
        }
        $strQuery = rtrim($strQuery," $condition ");

        $query = self::$db->prepare($strQuery);
        $query->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $success = $query->execute($params);

        if ($success) {
            return $query->fetch();
        }
        return false;
    }

    /**
     * @param array $params
     * @return $this[]|bool
     */
    public static function getAll($params = [])
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

        $strQuery = "SELECT * FROM ".self::getTableName();
        if (count(array_keys($params)) >= 1) {
            $strQuery .= " WHERE ";
            foreach ($params as $name => $value) {
                $strQuery .= "$name=:$name AND ";
            }
            $strQuery = rtrim($strQuery, " AND ");
        }

        $query = self::$db->prepare($strQuery.$limitQuery);
        $query->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $success = $query->execute($params);

        if ($success) {
            return $query->fetchAll();
        }
        return false;
    }

    /**
     * @return int|bool
     */
    public static function countAll()
    {
        $query = self::$db->prepare("SELECT COUNT(*) FROM ".self::getTableName());
        $success = $query->execute();
        if ($success) {
            return (int)$query->fetch()->{"COUNT(*)"};
        }
        return false;
    }

    /**
     * @param array $data
     * @return Entity|bool
     */
    public static function create($data)
    {
        unset($data["id"]);
        $data["creation_datetime"] = date("Y-m-d H:i:s");

        $strQuery = "INSERT INTO ".self::getTableName()." (";
        foreach ($data as $key => $value) {
            $strQuery .= "$key, ";
        }
        $strQuery = rtrim($strQuery, ", ").") ";

        $strQuery .= "VALUES (";
        foreach ($data as $key => $value) {
            $strQuery .= ":$key, ";
        }
        $strQuery = rtrim($strQuery, ", ").")";

        $query = self::$db->prepare($strQuery);
        $success = $query->execute($data);

        if ($success) {
            return self::get(["id" => self::$db->lastInsertId()]);
        }
        return false;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function update($data)
    {
        $strQuery = "UPDATE ".self::getTableName()." SET ";
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

    /**
     * @return bool
     */
    public function delete()
    {
        $query = self::$db->prepare("DELETE FROM ".self::getTableName()." WHERE id = ?");
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
}
