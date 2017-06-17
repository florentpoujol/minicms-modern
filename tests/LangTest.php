<?php

use App\Lang;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class LangTest extends TestCase
{
    private static $dictionaries;

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

        self::$dictionaries["en"] = require $root->url()."/en.php";
        self::$dictionaries["fr"] = require $root->url()."/fr.php";

        Lang::$defaultLanguage = "en";
        Lang::$currentLanguage = "fr";
        Lang::$languageFolder = $root->url()."/";
    }

    public function testLoad()
    {
        self::assertAttributeEmpty("dictionaries", Lang::class);

        self::assertTrue(Lang::load("en"));
        self::assertAttributeContains(self::$dictionaries["en"], "dictionaries", Lang::class);

        self::assertTrue(Lang::load("fr"));
        self::assertAttributeContains(self::$dictionaries["fr"], "dictionaries", Lang::class);

        self::assertFalse(Lang::load("de"));
    }

    public function testLang()
    {
        self::assertEquals(
            self::$dictionaries[Lang::$currentLanguage]["key"],
            Lang::get("key")
        );
        self::assertEquals("otherkey", Lang::get("otherkey")); // "otherkey" leads to an array, not a string

        self::assertEquals("nonexistentkey", Lang::get("nonexistentkey"));
        self::assertEquals("fr.nonexistentkey", Lang::get("fr.nonexistentkey"));
        self::assertEquals("en.nonexistentkey", Lang::get("en.nonexistentkey"));

        self::assertEquals(
            self::$dictionaries[Lang::$currentLanguage]["otherkey"]["nestedkey"],
            Lang::get("otherkey.nestedkey")
        );
        self::assertEquals("otherkey.nonexistentkey", Lang::get("otherkey.nonexistentkey"));

        self::assertEquals(
            self::$dictionaries[Lang::$defaultLanguage]["onlyindefaultlangkey"],
            Lang::get("onlyindefaultlangkey")
        );

        self::assertEquals(
            self::$dictionaries["en"]["key"],
            Lang::get("en.key")
        );
        self::assertEquals(
            self::$dictionaries["fr"]["key"],
            Lang::get("fr.key")
        );
        self::assertEquals("de.key", Lang::get("de.key"));

        self::assertEquals(
            str_replace(
                "{value}",
                "Florent",
                self::$dictionaries[Lang::$currentLanguage]["otherkey"]["nestedkey"]
            ),
            Lang::get("otherkey.nestedkey", ["value" => "Florent"])
        );
        self::assertEquals(
            str_replace(
                "{value}",
                "Nestor",
                self::$dictionaries["en"]["onlyindefaultlangkey"]
            ),
            Lang::get("onlyindefaultlangkey", ["value" => "Nestor"])
        );
    }

    public static function tearDownAfterClass()
    {
        Lang::$currentLanguage = "en";
        Lang::$languageFolder = __DIR__."/../languages/";
        App\Lang::load("en", true);
    }
}
