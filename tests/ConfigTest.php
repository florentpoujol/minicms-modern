<?php

use App\Config;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class ConfigTest extends TestCase
{
    private static $configFolder;

    /**
     * @var Config
     */
    private static $configManager;

    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private static $root;

    public static function setUpBeforeClass()
    {
        $root = vfsStream::setup(null, null, ["full" => [], "empty" => []]);
        self::$root = $root;

        $configFile = vfsStream::newFile("full/config.json")->at($root);
        $content = '{"bd_host": "localhost"}';
        $configFile->withContent($content);

        self::$configFolder = $root->url()."/full/";
        self::$configManager = new Config(self::$configFolder);
    }

    public function testLoad()
    {
        self::assertFileExists(self::$configFolder . "config.json");
        self::assertInternalType("boolean", self::$configManager->load());
        self::assertAttributeNotEmpty("config", self::$configManager);
    }

    /**
     * @depends testLoad
     */
    public function testGet()
    {
        self::assertEquals(null, self::$configManager->get("nonexistentkey"));
        self::assertEquals("defaultValue", self::$configManager->get("nonexistentkey", "defaultValue"));
        self::assertEquals("localhost", self::$configManager->get("bd_host"));
    }

    public function testSet()
    {
        self::assertEquals(null, self::$configManager->get("nonexistentkey"));
        self::$configManager->set("nonexistentkey", "avalue");
        self::assertEquals("avalue", self::$configManager->get("nonexistentkey"));

        self::assertEquals("localhost", self::$configManager->get("bd_host"));
        self::$configManager->set("bd_host", "127.0.0.1");
        self::assertEquals("127.0.0.1", self::$configManager->get("bd_host"));
    }

    /**
     * @depends testSet
     */
    public function testSave()
    {
        self::assertInternalType("boolean", self::$configManager->save());

        // reload
        self::assertFileExists(self::$configFolder."config.json");
        self::$configManager = new Config(self::$configFolder);
        self::assertEquals("avalue", self::$configManager->get("nonexistentkey"));
        self::assertEquals("127.0.0.1", self::$configManager->get("bd_host"));
    }
}
