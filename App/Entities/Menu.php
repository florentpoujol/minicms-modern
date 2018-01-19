<?php

namespace App\Entities;

class Menu extends Entity
{
    public $title = "";
    public $json_structure = "[]";
    public $structure = [];
    public $in_use = -1;

    public function getStructure(): array
    {
        return json_decode($this->json_structure, true);
    }

    /**
     * Remove items from structure where name and target are empty
     * @param array $structure The structure array, passed by reference
     */
    public static function cleanStructure($structure): array
    {
        for ($i = count($structure)-1; $i >= 0; $i--) {
            if (isset($structure[$i]["children"])) {
                $structure[$i]["children"] = self::cleanStructure($structure[$i]["children"]);
            }

            if (trim($structure[$i]["name"]) === "" && trim($structure[$i]["target"]) === "") {
                unset($structure[$i]);
            }
        }
        return $structure;
    }

    public function isInUse(): bool
    {
        return $this->in_use === 1;
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array["structure"] = $this->getStructure();
        return $array;
    }
}
