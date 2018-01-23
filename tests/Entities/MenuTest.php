<?php

namespace Tests;

use App\Entities\Menu;

class MenuTest extends DatabaseTestCase
{
    public function testGet()
    {
        $menu = $this->menuRepo->get(1);
        $this->assertInstanceOf(Menu::class, $menu);

        $this->assertSame("The first menu", $menu->title);
        $this->assertSame(false, $menu->isInUse());

        $menus = $this->menuRepo->getAll(["in_use" => false]);
        $this->assertCount(1, $menus);
        $this->assertContainsOnlyInstancesOf(Menu::class, $menus);
        $menus = $this->menuRepo->getAll(["in_use" => true]);
        $this->assertEmpty($menus);

        $this->assertSame(1, $this->menuRepo->countAll());
    }

    public function testCreate()
    {
        $this->assertSame(1, $this->menuRepo->countAll());

        $structure = [
            [
                "type" => "external",
                "name" => "Login/Admin",
                "target" => "?r=admin",
                "children" => []
            ]
        ]; // same as the default menu during install

        $newMenu = [
            "title" => "The second menu",
            "structure" => $structure,
            "in_use" => 1,
        ];
        $menu = $this->menuRepo->create($newMenu);
        $this->assertInstanceOf(Menu::class, $menu);
        $this->assertSame(2, $menu->id);
        $this->assertSame($structure, $menu->getStructure());
        $this->assertSame(json_encode($structure, JSON_PRETTY_PRINT), $menu->json_structure);
        $this->assertSame(2, $this->menuRepo->countAll());
    }

    public function testUpdate()
    {
        $menu = $this->menuRepo->get(1);

        $structure = $menu->getStructure();
        $this->assertCount(1, $structure);
        $this->assertSame("external", $structure[0]["type"]);
        $this->assertSame("?r=admin", $structure[0]["target"]);

        $structure[0]["type"] = "page";
        $structure[0]["target"] = "1"; // link to the page ith id 0
        $newMenu = [
            "title" => "The modified title",
            "structure" => $structure,
            "in_use" => true,
        ];
        $this->assertTrue($menu->update($newMenu));

        $this->assertSame("The modified title", $menu->title);
        $this->assertSame(true, $menu->in_use);
        $this->assertSame($structure, $menu->getStructure());

        $menu = $this->menuRepo->get(1);
        $this->assertSame("The modified title", $menu->title);
        $this->assertSame(1, $menu->in_use);
        $this->assertSame($structure, $menu->getStructure());
    }

    public function testDelete()
    {
        $menu = $this->menuRepo->get(1);
        $this->assertTrue($menu->delete());

        $this->assertSame(true, $menu->isDeleted);
        $this->assertSame(0, $this->menuRepo->countAll());
    }
}
