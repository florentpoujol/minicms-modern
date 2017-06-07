<?php

use \App\Security;
use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    public function testUniqueToken()
    {
        self::assertNotEquals(Security::getUniqueToken(), Security::getUniqueToken(), "two calls returns the same value");
        self::assertNotEquals(Security::getUniqueToken(30), Security::getUniqueToken(30), "two calls (with specified length) returns the same value");
        self::assertEquals(30, strlen(Security::getUniqueToken(30)), "the returned string is no of the specified length"); // /!\ not true for all lengths ! (ie: 25 will return length of 24)
    }

    public function testCSRFTokensCreation()
    {

        $token1 = Security::createCSRFTokens("request1");
        $token2 = Security::createCSRFTokens("request2");

        self::assertNotEquals($token1, $token2, "the two returned tokens should not be the same");

        self::assertArrayHasKey("request1_csrf_token", $_SESSION, "the session is missing the token key for request1");
        self::assertArrayHasKey("request1_csrf_time", $_SESSION, "the session is missing the time key for request1");
        self::assertEquals($token1, $_SESSION["request1_csrf_token"], "the returned token is not the same as the one stored in session for request1");
        self::assertInternalType("int", $_SESSION["request1_csrf_time"], "the time stored in session is not an int for request1");

        self::assertArrayHasKey("request2_csrf_token", $_SESSION, "the session is missing the token key for request2");
        self::assertArrayHasKey("request2_csrf_time", $_SESSION, "the session is missing the time key for request2");
        self::assertEquals($token2, $_SESSION["request2_csrf_token"], "the returned token is not the same as the one stored in session for request2");
        self::assertInternalType("int", $_SESSION["request2_csrf_time"], "the time stored in session is not an int for request2");

        $token3 = Security::createCSRFTokens("request2");
        self::assertNotEquals($token2, $_SESSION["request2_csrf_token"], "the session token for request2 is still the same");
        self::assertEquals($token3, $_SESSION["request2_csrf_token"], "the returned token3 is not the same as the one stored in session for request2");
    }
}
