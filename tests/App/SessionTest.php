<?php

namespace Tests;

use App\Helpers;

class SessionTest extends BaseTestCase
{
    public function testId()
    {
        self::assertInternalType("string", $this->session->getId());
        self::assertNotEmpty($this->session->getId());
        self::assertEquals($this->session->getId(), $this->session->getId());
    }

    public function testGet()
    {
        self::assertEquals(null, $this->session->get("nonExistentKey"));
        self::assertEquals("defaultvalue", $this->session->get("nonExistentKey", "defaultvalue"));

        $_SESSION["thekey"] = 10;
        self::assertEquals(10, $this->session->get("thekey"));
        self::assertEquals(10, $this->session->get("thekey", "defaultvalue"));
    }

    /**
     * @depends testGet
     */
    public function testSet()
    {
        self::assertEquals(null, $this->session->get("nonExistentKey"));
        $this->session->set("nonExistentKey", true);
        self::assertEquals(true, $this->session->get("nonExistentKey"));
    }

    /**
     * @depends testSet
     */
    public function testDestroy()
    {
        self::assertEquals(true, $this->session->get("nonExistentKey"));
        $this->session->delete("nonExistentKey");
        self::assertEquals(null, $this->session->get("nonExistentKey"));
        $this->session->delete("nonExistentKey"); // test doesn't throw error

        self::assertNotEmpty($_SESSION);
        self::assertFalse(empty($_SESSION));
        $this->session->destroy();
        self::assertTrue(empty($_SESSION));
        self::assertEmpty($this->session->getId());
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
        $token1 = $this->session->createCSRFToken("request1");
        $token2 = $this->session->createCSRFToken("request2");

        self::assertNotEquals($token1, $token2, "the two returned tokens should not be the same");

        self::assertArrayHasKey("request1_csrf_token", $_SESSION, "the session is missing the token key for request1");
        self::assertArrayHasKey("request1_csrf_time", $_SESSION, "the session is missing the time key for request1");
        self::assertEquals($token1, $_SESSION["request1_csrf_token"], "the returned token is not the same as the one stored in session for request1");
        self::assertInternalType("int", $_SESSION["request1_csrf_time"], "the time stored in session is not an int for request1");

        self::assertArrayHasKey("request2_csrf_token", $_SESSION, "the session is missing the token key for request2");
        self::assertArrayHasKey("request2_csrf_time", $_SESSION, "the session is missing the time key for request2");
        self::assertEquals($token2, $_SESSION["request2_csrf_token"], "the returned token is not the same as the one stored in session for request2");
        self::assertInternalType("int", $_SESSION["request2_csrf_time"], "the time stored in session is not an int for request2");

        $token3 = $this->session->createCSRFToken("request2");
        self::assertNotEquals($token2, $_SESSION["request2_csrf_token"], "the session token for request2 is still the same");
        self::assertEquals($token3, $_SESSION["request2_csrf_token"], "the returned token3 is not the same as the one stored in session for request2");
    }

    public function testFlashSuccesses()
    {
        $this->session->addSuccess("the success message");
        $this->session->addSuccess("user.loggedin");
        $this->session->addSuccess("user.loggedin", ["username" => "Florent"]);

        $msgs = $this->session->getSuccesses();
        self::assertEmpty($this->session->getSuccesses());
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
        $this->session->addError("the error message");
        $this->session->addError("user.unknownwithfield");
        $this->session->addError("user.unknownwithfield", ["field" => "name", "value" => "Florent"]);

        $msgs = $this->session->getErrors();
        self::assertEmpty($this->session->getErrors());
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
