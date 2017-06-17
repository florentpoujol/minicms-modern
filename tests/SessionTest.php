<?php

use App\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    public function testId()
    {
        self::assertInternalType("string", Session::getId());
        self::assertNotEmpty(Session::getId());
        self::assertEquals(Session::getId(), Session::getId());
    }

    public function testGet()
    {
        self::assertEquals(null, Session::get("nonExistentKey"));
        self::assertEquals("defaultvalue", Session::get("nonExistentKey", "defaultvalue"));

        $_SESSION["thekey"] = 10;
        self::assertEquals(10, Session::get("thekey"));
        self::assertEquals(10, Session::get("thekey", "defaultvalue"));
    }

    /**
     * @depends testGet
     */
    public function testSet()
    {
        self::assertEquals(null, Session::get("nonExistentKey"));
        Session::set("nonExistentKey", true);
        self::assertEquals(true, Session::get("nonExistentKey"));
    }

    /**
     * @depends testSet
     */
    public function testDestroy()
    {
        self::assertEquals(true, Session::get("nonExistentKey"));
        Session::destroy("nonExistentKey");
        self::assertEquals(null, Session::get("nonExistentKey"));
        Session::destroy("nonExistentKey"); // test doesn't throw error

        self::assertNotEmpty($_SESSION);
        self::assertTrue(isset($_SESSION));
        Session::destroy();
        self::assertFalse(isset($_SESSION));
        self::assertEmpty(Session::getId());
    }
}
