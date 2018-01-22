<?php

namespace Tests;

class ValidatorTest extends BaseTestCase
{
    public function testValidate()
    {
        self::assertTrue($this->validator->title("This is : a title !"));

        self::assertTrue($this->validator->name("Florent"));

        self::assertTrue($this->validator->slug("florent2"));

        self::assertTrue($this->validator->email("email@email.fr"));
        self::assertTrue($this->validator->email("em.ail2@email.com.nf"));

        $pass = "thePassWord5!";
        $pass2 = "thePassWord5!";
        self::assertTrue($this->validator->password($pass));
        self::assertTrue($this->validator->password($pass, $pass2));


        self::assertFalse($this->validator->title("t"));
        self::assertFalse($this->validator->title("This is ; a title §"));

        self::assertFalse($this->validator->name("Flo rent"));

        self::assertFalse($this->validator->slug("Flo rent"));
        self::assertFalse($this->validator->slug("florent!"));

        self::assertFalse($this->validator->email("emailemail.fr"));
        self::assertFalse($this->validator->email("email@email"));

        self::assertFalse($this->validator->password("thePass"));
        self::assertFalse($this->validator->password("465"));
        self::assertFalse($this->validator->password("aZ12", "aZ1"));
    }

    public function testCSRF()
    {
        self::assertFalse($this->validator->csrf("unknowrequest", "unknowtoken"));
        self::assertFalse($this->validator->csrf("unknowrequest"));

        $_SESSION["therequest_csrf_token"] = "thetoken";
        $_SESSION["therequest_csrf_time"] = time() - 60;
        self::assertFalse($this->validator->csrf("therequest", "thewrongtoken"));
        self::assertFalse($this->validator->csrf("therequest", "thetoken", 10));

        self::assertTrue($this->validator->csrf("therequest", "thetoken", 100));
        self::assertArrayNotHasKey("therequest_csrf_token", $_SESSION);
        self::assertArrayNotHasKey("therequest_csrf_time", $_SESSION);

        $_SESSION["therequest_csrf_token"] = "thetoken";
        $_SESSION["therequest_csrf_time"] = time() - 60;
        self::assertTrue($this->validator->csrf("therequest", "thetoken")); // default time limit = 900

        $_SESSION["therequest_csrf_token"] = "thetoken";
        $_SESSION["therequest_csrf_time"] = time() - 60;
        self::assertFalse($this->validator->csrf("therequest"));
        $_POST["therequest_csrf_token"] = "thetoken";
        self::assertTrue($this->validator->csrf("therequest"));
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
            "nonexistentkey" => "string"
        ];

        $post = $this->validator->sanitizePost($schema);

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
        self::assertInternalType("string", $post["nonexistentkey"]);
        self::assertSame("", $post["nonexistentkey"]);

        self::assertArrayNotHasKey("garbage", $post);
        self::assertArrayNotHasKey("garbage2", $post);
    }
}
