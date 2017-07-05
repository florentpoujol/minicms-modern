<?php
use App\Messages;

class MessagesTest extends DatabaseTestCase
{
    public function setUp() {
        \App\Lang::load("en", true);
    }

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

    public function save()
    {
        Messages::addSuccess("the success message");
        Messages::addError("the error message");

        self::assertEquals(0, $this->getConnection()->getRowCount("messages"));
        Messages::save();
        self::assertEquals(2, $this->getConnection()->getRowCount("messages"));

        $dataSet = new \PHPUnit\DbUnit\DataSet\YamlDataSet(__dir__."/messagesDataSet.yml");
        $expectedTable = $dataSet->getTable("messages");
        $queryTable = $this->getConnection()->createQueryTable("messages", "SELECT type, content FROM messages ORDER BY id ASC");

        self::assertTablesEqual($expectedTable, $queryTable);
    }

    public function testLoad()
    {
        $this->save();

        Messages::load();
        self::assertEquals(0, $this->getConnection()->getRowCount("messages"));

        $msgs = Messages::getSuccesses();
        self::assertCount(1, $msgs);
        self::assertEquals("the success message", $msgs[0]);

        $msgs = Messages::getErrors();
        self::assertCount(1, $msgs);
        self::assertEquals("the error message", $msgs[0]);
    }
}
