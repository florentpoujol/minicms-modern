<?php

use App\Config;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class ConfigTest extends TestCase
{
    private $configFolder;
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $root;

    public function setUp()
    {
        $root = vfsStream::setup(null, null, ["full" => [], "empty" => []]);
        $this->root = $root;

        $configFile = vfsStream::newFile("full/config.json")->at($root);
        $content = '{"bd_host": "localhost"}';
        $configFile->withContent($content);

        $this->configFolder = $root->url()."/full/";
        Config::$configFolder = $this->configFolder;
    }

    public function testLoad()
    {
        self::assertFileExists($this->configFolder."config.json");
        self::assertAttributeEmpty("config", Config::class);
        self::assertInternalType("boolean", Config::load());
        self::assertAttributeNotEmpty("config", Config::class);
    }

    /**
     * @depends testLoad
     */
    public function testGet()
    {
        self::assertFileExists($this->configFolder."config.json");
        self::assertEquals(null, Config::get("qfqfqfqdf"));
        self::assertEquals("defaultValue", Config::get("qfqfqfqdf", "defaultValue"));
        self::assertEquals("localhost", Config::get("bd_host"));
    }

    public function testSet()
    {
        self::assertFileExists($this->configFolder."config.json");
        self::assertEquals(null, Config::get("qfqfqfqdf"));
        Config::set("qfqfqfqdf", "avalue");
        self::assertEquals("avalue", Config::get("qfqfqfqdf"));

        self::assertEquals("localhost", Config::get("bd_host"));
        Config::set("bd_host", "127.0.0.1");
        self::assertEquals("127.0.0.1", Config::get("bd_host"));
    }

    /**
     * @depends testSet
     */
    public function testSave()
    {
        self::assertFileExists($this->configFolder."config.json");
        self::assertInternalType("boolean", Config::save());
        self::assertFileExists($this->configFolder."config.json");

        // flush the static $config array in the class by loading an empty file
        $emptyConfig = vfsStream::newFile("empty/config.json")->at($this->root);
        $emptyConfig->withContent('{}');
        Config::$configFolder = $this->root->url()."/empty/";
        Config::load();

        self::assertAttributeEmpty("config", Config::class); // check flushing

        // reload
        self::assertFileExists($this->configFolder."config.json");
        Config::$configFolder = $this->configFolder;
        self::assertFileExists($this->configFolder."config.json");
        Config::load();
        self::assertEquals("avalue", Config::get("qfqfqfqdf"));
        self::assertEquals("127.0.0.1", Config::get("bd_host"));
    }
}
