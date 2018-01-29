<?php

namespace Tests;

use App\Config;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class ConfigTest extends TestCase
{
    private static $configFilePath = "";

    /**
     * @var Config
     */
    private static $config;

    public static function setUpBeforeClass()
    {
        $root = vfsStream::setup();

        $configFile = vfsStream::newFile("config.json")->at($root);
        $content = '{"bd_host": "localhost"}';
        $configFile->withContent($content);

        self::$configFilePath = $root->url() . "/config.json";
        self::$config = new Config(self::$configFilePath);
        self::$config->load();
    }

    public function testLoad()
    {
        $this->assertFileExists(self::$configFilePath);
        $this->assertSame(true, self::$config->load());
    }

    public function testGet()
    {
        $this->assertSame(null, self::$config->get("nonexistentkey"));
        $this->assertSame("defaultValue", self::$config->get("nonexistentkey", "defaultValue"));
        $this->assertSame("localhost", self::$config->get("bd_host"));
    }

    public function testSet()
    {
        $this->assertEquals(null, self::$config->get("nonexistentkey"));
        self::$config->set("nonexistentkey", "avalue");
        $this->assertEquals("avalue", self::$config->get("nonexistentkey"));

        $this->assertEquals("localhost", self::$config->get("bd_host"));
        self::$config->set("bd_host", "127.0.0.1");
        $this->assertEquals("127.0.0.1", self::$config->get("bd_host"));
    }

    public function testSave()
    {
        $this->assertInternalType("boolean", self::$config->save());

        // reload
        $this->assertFileExists(self::$configFilePath);
        self::$config = new Config(self::$configFilePath);
        self::$config->load();
        $this->assertEquals("avalue", self::$config->get("nonexistentkey"));
        $this->assertEquals("127.0.0.1", self::$config->get("bd_host"));
    }
}
