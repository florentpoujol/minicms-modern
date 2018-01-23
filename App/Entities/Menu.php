<?php

namespace App\Entities;

use App\Router;
use App\Entities\Repositories\Menu as MenuRepo;

class Menu extends Entity
{
    public $title = "";
    public $json_structure = "[]";
    public $structure = []; // field not in DB, populated manually
    public $in_use = -1;

    /**
     * @var MenuRepo
     */
    public $menuRepo;

    public function __construct(Router $router, MenuRepo $menuRepo)
    {
        parent::__construct($router);
        $this->menuRepo = $menuRepo;
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

    public function hydrate(array $data)
    {
        parent::hydrate($data);
        $this->structure = $this->getStructure();
    }

    public function update(array $data): bool
    {
        $data["json_structure"] = json_encode(
            self::cleanStructure($data["structure"]),
            JSON_PRETTY_PRINT
        ); // this is done here to that the new json_structure gets hydrated properly
        unset($data["structure"]);
        return parent::update($data);
    }

    public function getStructure(): array
    {
        return json_decode($this->json_structure, true);
    }

    public function isInUse(): bool
    {
        return $this->in_use === 1;
    }
}
