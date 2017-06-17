<?php
/**
 * Created by PhpStorm.
 * User: Florent Poujol
 * Date: 11/06/2017
 * Time: 15:57
 */

use App\Messages;
use PHPUnit\Framework\TestCase;

class MessagesTest extends TestCase
{
    public function testSuccess()
    {
        Messages::addSuccess("the success message");
        Messages::addSuccess("user.loggedin");
        Messages::addSuccess("user.loggedin", ["username" => "Florent"]);

        $msgs = Messages::getSuccesses();
        self::assertEmpty(Messages::getSuccesses());
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

    public function testError()
    {
        Messages::addError("the error message");
        Messages::addError("user.unknownwithfield");
        Messages::addError("user.unknownwithfield", ["field" => "name", "value" => "Florent"]);

        $msgs = Messages::getErrors();
        self::assertEmpty(Messages::getErrors());
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

    public function testLoad()
    {
        self::markTestIncomplete();
    }

    public function testSave()
    {
        self::markTestIncomplete();
    }
}
