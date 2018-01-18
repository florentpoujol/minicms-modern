<?php

use App\Session;
use App\Helpers;
use App\Lang;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{

    public function testId()
    {
        $session = new Session(new Helpers(), new Lang());
        self::assertInternalType("string", $session->getId());
        self::assertNotEmpty($session->getId());
        self::assertEquals($session->getId(), $session->getId());
    }

    public function testGet()
    {
        $session = new Session(new Helpers(), new Lang());
        self::assertEquals(null, $session->get("nonExistentKey"));
        self::assertEquals("defaultvalue", $session->get("nonExistentKey", "defaultvalue"));

        $_SESSION["thekey"] = 10;
        self::assertEquals(10, $session->get("thekey"));
        self::assertEquals(10, $session->get("thekey", "defaultvalue"));
    }

    /**
     * @depends testGet
     */
    public function testSet()
    {
        $session = new Session(new Helpers(), new Lang());
        self::assertEquals(null, $session->get("nonExistentKey"));
        $session->set("nonExistentKey", true);
        self::assertEquals(true, $session->get("nonExistentKey"));
    }

    /**
     * @depends testSet
     */
    public function testDestroy()
    {
        $session = new Session(new Helpers(), new Lang());
        self::assertEquals(true, $session->get("nonExistentKey"));
        $session->delete("nonExistentKey");
        self::assertEquals(null, $session->get("nonExistentKey"));
        $session->delete("nonExistentKey"); // test doesn't throw error

        self::assertNotEmpty($_SESSION);
        self::assertFalse(empty($_SESSION));
        $session->destroy();
        self::assertTrue(empty($_SESSION));
        self::assertEmpty($session->getId());
    }

    public function testUniqueToken()
    {
        $helpers = new Helpers();
        self::assertNotEquals($helpers->getUniqueToken(), $helpers->getUniqueToken(), "two calls returns the same value");
        self::assertNotEquals($helpers->getUniqueToken(30), $helpers->getUniqueToken(30), "two calls (with specified length) returns the same value");
        self::assertEquals(30, strlen($helpers->getUniqueToken(30)), "the returned string is no of the specified length"); // /!\ not true for all lengths ! (ie: 25 will return length of 24)
    }

    public function testCSRFTokensCreation()
    {
        $session = new Session(new Helpers(), new Lang());
        $token1 = $session->createCSRFToken("request1");
        $token2 = $session->createCSRFToken("request2");

        self::assertNotEquals($token1, $token2, "the two returned tokens should not be the same");

        self::assertArrayHasKey("request1_csrf_token", $_SESSION, "the session is missing the token key for request1");
        self::assertArrayHasKey("request1_csrf_time", $_SESSION, "the session is missing the time key for request1");
        self::assertEquals($token1, $_SESSION["request1_csrf_token"], "the returned token is not the same as the one stored in session for request1");
        self::assertInternalType("int", $_SESSION["request1_csrf_time"], "the time stored in session is not an int for request1");

        self::assertArrayHasKey("request2_csrf_token", $_SESSION, "the session is missing the token key for request2");
        self::assertArrayHasKey("request2_csrf_time", $_SESSION, "the session is missing the time key for request2");
        self::assertEquals($token2, $_SESSION["request2_csrf_token"], "the returned token is not the same as the one stored in session for request2");
        self::assertInternalType("int", $_SESSION["request2_csrf_time"], "the time stored in session is not an int for request2");

        $token3 = $session->createCSRFToken("request2");
        self::assertNotEquals($token2, $_SESSION["request2_csrf_token"], "the session token for request2 is still the same");
        self::assertEquals($token3, $_SESSION["request2_csrf_token"], "the returned token3 is not the same as the one stored in session for request2");
    }

    public function testFlashSuccesses()
    {
        $lang = new Lang();
        $lang->load("en");
        $session = new Session(new Helpers(), $lang);

        $session->addSuccess("the success message");
        $session->addSuccess("user.loggedin");
        $session->addSuccess("user.loggedin", ["username" => "Florent"]);

        $msgs = $session->getSuccesses();
        self::assertEmpty($session->getSuccesses());
        self::assertNotEmpty($msgs);
        self::assertCount(3, $msgs);

        $expected = [
            "the success message",
            "Welcome {username}, you are now logged in",
            "Welcome Florent, you are now logged in"
        ];

        foreach ($msgs as $id => $msg) {
            self::assertEquals($expected[$id], $msg);
        }
    }

    public function testFlashErrors()
    {
        $lang = new Lang();
        $lang->load("en");
        $session = new Session(new Helpers(), $lang);

        $session->addError("the error message");
        $session->addError("user.unknownwithfield");
        $session->addError("user.unknownwithfield", ["field" => "name", "value" => "Florent"]);

        $msgs = $session->getErrors();
        self::assertEmpty($session->getErrors());
        self::assertNotEmpty($msgs);
        self::assertCount(3, $msgs);

        $expected = [
            "the error message",
            "Unknow user with {field} '{value}'",
            "Unknow user with name 'Florent'"
        ];

        foreach ($msgs as $id => $msg) {
            self::assertEquals($expected[$id], $msg);
        }
    }
}
