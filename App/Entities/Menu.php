<?php

namespace App\Entities;

class Menu extends Entity
{
    public $name;
    public $json_structure;
    public $structure = [];

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

    /**
     * @return $this|false
     */
    public static function create(array $data)
    {
        $data["structure"] = self::cleanStructure($data["structure"]);
        $data["json_structure"] = json_encode($data["structure"], JSON_PRETTY_PRINT);
        unset($data["structure"]);
        return parent::create($data);
    }

    public function update(array $data): bool
    {
        $data["structure"] = self::cleanStructure($data["structure"]);
        $data["json_structure"] = json_encode($data["structure"], JSON_PRETTY_PRINT);
        unset($data["structure"]);
        return parent::update($data);
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array["structure"] = $this->getStructure();
        return $array;
    }
}