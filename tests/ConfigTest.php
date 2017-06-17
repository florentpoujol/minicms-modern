<?php

use App\Config;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class ConfigTest extends TestCase
{
    private static $configFolder;
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
        Config::$configFolder = self::$configFolder;
    }

    public function testLoad()
    {
        self::assertFileExists(self::$configFolder."config.json");
        self::assertAttributeEmpty("config", Config::class);
        self::assertInternalType("boolean", Config::load());
        self::assertAttributeNotEmpty("config", Config::class);
    }

    /**
     * @depends testLoad
     */
    public function testGet()
    {
        self::assertEquals(null, Config::get("nonexistentkey"));
        self::assertEquals("defaultValue", Config::get("nonexistentkey", "defaultValue"));
        self::assertEquals("localhost", Config::get("bd_host"));
    }

    public function testSet()
    {
        self::assertEquals(null, Config::get("nonexistentkey"));
        Config::set("nonexistentkey", "avalue");
        self::assertEquals("avalue", Config::get("nonexistentkey"));

        self::assertEquals("localhost", Config::get("bd_host"));
        Config::set("bd_host", "127.0.0.1");
        self::assertEquals("127.0.0.1", Config::get("bd_host"));
    }

    /**
     * @depends testSet
     */
    public function testSave()
    {
        self::assertInternalType("boolean", Config::save());

        // flush the static $config array in the class by loading an empty file
        $emptyConfig = vfsStream::newFile("empty/config.json")->at(self::$root);
        $emptyConfig->withContent('{}');
        Config::$configFolder = self::$root->url()."/empty/";
        Config::load();

        self::assertAttributeEmpty("config", Config::class); // check flushing

        // reload
        Config::$configFolder = self::$configFolder;
        self::assertFileExists(self::$configFolder."config.json");
        Config::load();
        self::assertEquals("avalue", Config::get("nonexistentkey"));
        self::assertEquals("127.0.0.1", Config::get("bd_host"));
    }
}
