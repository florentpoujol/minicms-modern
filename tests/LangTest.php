<?php

namespace Tests;

use App\Lang;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class LangTest extends TestCase
{
    private static $dictionaries;
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

        self::$languageFolder = $root->url() . "/";
        self::$dictionaries["en"] = require $root->url() . "/en.php";
        self::$dictionaries["fr"] = require $root->url() . "/fr.php";
    }

    public function testLoad()
    {
        $lang = new Lang();
        $lang->languageFolder = self::$languageFolder;

        self::assertAttributeEmpty("dictionariesPerLocale", $lang);

        self::assertTrue($lang->load("en"));
        self::assertAttributeContains(self::$dictionaries["en"], "dictionariesPerLocale", $lang);

        self::assertTrue($lang->load("fr"));
        self::assertAttributeContains(self::$dictionaries["fr"], "dictionariesPerLocale", $lang);

        self::assertFalse($lang->load("de"));
    }

    public function testLang()
    {
        $lang = new Lang();
        $lang->languageFolder = self::$languageFolder;
        $lang->currentLanguage = "fr";
        $lang->load("en");
        $lang->load("fr");

        self::assertEquals(
            self::$dictionaries[$lang->currentLanguage]["key"],
            $lang->get("key")
        );
        self::assertEquals("otherkey", $lang->get("otherkey")); // "otherkey" leads to an array, not a string

        self::assertEquals("nonexistentkey", $lang->get("nonexistentkey"));
        self::assertEquals("fr.nonexistentkey", $lang->get("fr.nonexistentkey"));
        self::assertEquals("en.nonexistentkey", $lang->get("en.nonexistentkey"));

        self::assertEquals(
            self::$dictionaries[$lang->currentLanguage]["otherkey"]["nestedkey"],
            $lang->get("otherkey.nestedkey")
        );
        self::assertEquals("otherkey.nonexistentkey", $lang->get("otherkey.nonexistentkey"));

        self::assertEquals(
            self::$dictionaries[$lang->defaultLanguage]["onlyindefaultlangkey"],
            $lang->get("onlyindefaultlangkey")
        );

        self::assertEquals(
            self::$dictionaries["en"]["key"],
            $lang->get("en.key")
        );
        self::assertEquals(
            self::$dictionaries["fr"]["key"],
            $lang->get("fr.key")
        );
        self::assertEquals("de.key", $lang->get("de.key"));

        self::assertEquals(
            str_replace(
                "{value}",
                "Florent",
                self::$dictionaries[$lang->currentLanguage]["otherkey"]["nestedkey"]
            ),
            $lang->get("otherkey.nestedkey", ["value" => "Florent"])
        );
        self::assertEquals(
            str_replace(
                "{value}",
                "Nestor",
                self::$dictionaries["en"]["onlyindefaultlangkey"]
            ),
            $lang->get("onlyindefaultlangkey", ["value" => "Nestor"])
        );
    }
}
