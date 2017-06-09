<?php

use App\Validate;
use PHPUnit\Framework\TestCase;

class ValidateTest extends TestCase
{
    public function testValidate()
    {
        self::assertTrue(Validate::title("This is : a title !"));

        self::assertTrue(Validate::name("Florent"));

        self::assertTrue(Validate::slug("florent2"));

        self::assertTrue(Validate::email("email@email.fr"));
        self::assertTrue(Validate::email("em.ail2@email.com.nf"));

        $pass = "thePassWord5!";
        $pass2 = "thePassWord5!";
        self::assertTrue(Validate::password($pass));
        self::assertTrue(Validate::password($pass, $pass2));


        self::assertFalse(Validate::title("t"));
        self::assertFalse(Validate::title("This is ; a title §"));

        self::assertFalse(Validate::name("Flo rent"));

        self::assertFalse(Validate::slug("Flo rent"));
        self::assertFalse(Validate::slug("florent!"));

        self::assertFalse(Validate::email("emailemail.fr"));
        self::assertFalse(Validate::email("email@email"));

        self::assertFalse(Validate::password("thePass"));
        self::assertFalse(Validate::password("465"));
        self::assertFalse(Validate::password("aZ12", "aZ1"));
    }

    public function testCSRF()
    {
        self::assertFalse(Validate::csrf("unknowrequest", "unknowtoken"));
        self::assertFalse(Validate::csrf("unknowrequest"));
        self::assertFalse(Validate::csrf(null, null));

        $_SESSION["therequest_csrf_token"] = "thetoken";
        $_SESSION["therequest_csrf_time"] = time() - 60;
        self::assertFalse(Validate::csrf("therequest", "thewrongtoken"));
        self::assertFalse(Validate::csrf("therequest", "thetoken", 10));

        self::assertTrue(Validate::csrf("therequest", "thetoken", 100));
        self::assertArrayNotHasKey("therequest_csrf_token", $_SESSION);
        self::assertArrayNotHasKey("therequest_csrf_time", $_SESSION);

        $_SESSION["therequest_csrf_token"] = "thetoken";
        $_SESSION["therequest_csrf_time"] = time() - 60;
        self::assertTrue(Validate::csrf("therequest", "thetoken")); // default time limit = 900

        $_SESSION["therequest_csrf_token"] = "thetoken";
        $_SESSION["therequest_csrf_time"] = time() - 60;
        self::assertFalse(Validate::csrf("therequest"));
        $_POST["therequest_csrf_token"] = "thetoken";
        self::assertTrue(Validate::csrf("therequest"));
        self::assertArrayNotHasKey("therequest_csrf_token", $_POST);
    }

    public function testSanitizePost()
    {
        $_POST = [
            "int" => 1,
            "int2" => "2",
            "str" => "str",
            "str2" => 99,
            "bool" => true,
            "bool2" => 0,
            "garbage1" => 10,
            "garbage2" => []
        ];

        $schema = [
            "int" => "int",
            "int2" => "int",
            "str" => "string",
            "str2" => "string",
            "bool" => "bool",
            "bool2" => "bool",
            "nonexistentkey" => "str"
        ];

        $post = Validate::sanitizePost($schema);

        self::assertArrayHasKey("int", $post);
        self::assertInternalType("int", $post["int"]);
        self::assertArrayHasKey("int2", $post);
        self::assertInternalType("int", $post["int2"]);

        self::assertArrayHasKey("str", $post);
        self::assertInternalType("string", $post["str"]);
        self::assertArrayHasKey("str2", $post);
        self::assertInternalType("string", $post["str2"]);

        self::assertArrayHasKey("bool", $post);
        self::assertInternalType("bool", $post["bool"]);
        self::assertArrayHasKey("bool2", $post);
        self::assertInternalType("bool", $post["bool2"]);

        self::assertArrayHasKey("nonexistentkey", $post);
        self::assertNull($post["nonexistentkey"]);

        self::assertArrayNotHasKey("garbage", $post);
        self::assertArrayNotHasKey("garbage2", $post);
    }
}
