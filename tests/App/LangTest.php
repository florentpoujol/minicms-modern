<?php

namespace Tests;

use App\Lang;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class LangTest extends TestCase
{
    private static $dictionaries = [];
    private static $languageFolder = "";

    public static function setUpBeforeClass()
    {
        $root = vfsStream::setup();

        $en = vfsStream::newFile("en.php")->at($root);
        $content = '<?php
        return [
            "key" => "thevalue",
            "otherkey" => [
                "nestedkey" => "nested {value}"
            ],
            "onlyindefaultlangkey" => "onlyindefaultlang {value}"
        ];        
        ';
        $en->withContent($content);

        $fr = vfsStream::newFile("fr.php")->at($root);
        $content = '<?php
        return [
            "key" => "la valeur",
            "otherkey" => [
                "nestedkey" => "{value} imbriquée"
            ]
        ];        
        ';
        $fr->withContent($content);

        self::$languageFolder = $root->url();
        self::$dictionaries["en"] = require $root->url() . "/en.php";
        self::$dictionaries["fr"] = require $root->url() . "/fr.php";
    }

    public function testLoad()
    {
        $lang = new Lang(self::$languageFolder);

        $this->assertAttributeEmpty("dictionariesPerLocale", $lang);

        $this->assertTrue($lang->load("en"));
        $this->assertAttributeContains(self::$dictionaries["en"], "dictionariesPerLocale", $lang);

        $this->assertTrue($lang->load("fr"));
        $this->assertAttributeContains(self::$dictionaries["fr"], "dictionariesPerLocale", $lang);

        $this->assertFalse($lang->load("de"));
    }

    public function testLang()
    {
        $lang = new Lang(self::$languageFolder);
        $lang->currentLanguage = "fr";
        $lang->load("en");
        $lang->load("fr");

        $this->assertEquals(
            self::$dictionaries[$lang->currentLanguage]["key"],
            $lang->get("key")
        );
        $this->assertEquals("otherkey", $lang->get("otherkey")); // "otherkey" leads to an array, not a string

        $this->assertEquals("nonexistentkey", $lang->get("nonexistentkey"));
        $this->assertEquals("fr.nonexistentkey", $lang->get("fr.nonexistentkey"));
        $this->assertEquals("en.nonexistentkey", $lang->get("en.nonexistentkey"));

        $this->assertEquals(
            self::$dictionaries[$lang->currentLanguage]["otherkey"]["nestedkey"],
            $lang->get("otherkey.nestedkey")
        );
        $this->assertEquals("otherkey.nonexistentkey", $lang->get("otherkey.nonexistentkey"));

        $this->assertEquals(
            self::$dictionaries[$lang->defaultLanguage]["onlyindefaultlangkey"],
            $lang->get("onlyindefaultlangkey")
        );

        $this->assertEquals(
            self::$dictionaries["en"]["key"],
            $lang->get("en.key")
        );
        $this->assertEquals(
            self::$dictionaries["fr"]["key"],
            $lang->get("fr.key")
        );
        $this->assertEquals("de.key", $lang->get("de.key"));

        $this->assertEquals(
            str_replace(
                "{value}",
                "Florent",
                self::$dictionaries[$lang->currentLanguage]["otherkey"]["nestedkey"]
            ),
            $lang->get("otherkey.nestedkey", ["value" => "Florent"])
        );
        $this->assertEquals(
            str_replace(
                "{value}",
                "Nestor",
                self::$dictionaries["en"]["onlyindefaultlangkey"]
            ),
            $lang->get("onlyindefaultlangkey", ["value" => "Nestor"])
        );
    }
}
