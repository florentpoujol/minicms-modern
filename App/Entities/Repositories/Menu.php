<?php

namespace App\Entities\Repositories;

use App\Entities\Menu as MenuEntity;

class Menu extends Entity
{
    /**
     * @return MenuEntity|false
     */
    public function get($whereConditions, bool $useWhereOrOperator = false)
    {
        return parent::get($whereConditions, $useWhereOrOperator);
    }

    /**
     * @return MenuEntity[]|false
     */
    public function getAll(array $params = [])
    {
        return parent::getAll($params);
    }

    /**
     * @return MenuEntity|false
     */
    public function create(array $data)
    {
        $data["structure"] = MenuEntity::cleanStructure($data["structure"]);
        $data["json_structure"] = json_encode($data["structure"], JSON_PRETTY_PRINT);
        unset($data["structure"]);
        return parent::create($data);
    }

    /**
     * @param MenuEntity $menu
     */
    public function update($menu, array $data): bool
    {
        $data["structure"] = MenuEntity::cleanStructure($data["structure"]);
        $data["json_structure"] = json_encode($data["structure"], JSON_PRETTY_PRINT);
        unset($data["structure"]);
        return parent::update($menu, $data);
    }
}
