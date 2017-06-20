<?php

use App\Entities\User;

class UserTest extends DatabaseTestCase
{
    public function getDataSet()
    {
        return new \PHPUnit\DbUnit\DataSet\YamlDataSet(__dir__."/userDataSet.yml");
    }

    public function testGet()
    {
        $user = User::get(["id" => 999]);
        self::assertInternalType("bool", $user);
        self::assertEquals(false, $user);

        $user = User::get(["id" => 1]);
        self::assertInstanceOf(User::class, $user);
        self::assertEquals(true, $user->isAdmin());
        self::assertEquals(false, $user->isWriter());


        $user = User::get(["role" => "writer"]);
        self::assertInstanceOf(User::class, $user);
        self::assertEquals(true, $user->isWriter());
        self::assertEquals(false, $user->isCommenter());

        $user = User::get(["id" => 2, "role" => "commenter"]);
        self::assertEquals(false, $user);

        $user = User::get(["id" => 3, "role" => "commenter"]); // condition == AND
        self::assertInstanceOf(User::class, $user);
        self::assertEquals(true, $user->isCommenter());
    }

    public function testGetAll()
    {
        $users = User::getAll(["role" => "whatever"]);
        self::assertInternalType("array", $users);
        self::assertEmpty($users);

        $users = User::getAll();
        self::assertCount(3, $users);
        self::assertContainsOnlyInstancesOf(User::class, $users);

        $users = User::getAll(["role" => "writer"]);
        self::assertCount(1, $users);
        self::assertContainsOnlyInstancesOf(User::class, $users);
    }

    public function testCountAll()
    {
        self::assertEquals(3, User::countAll());
    }

    public function testGetResources()
    {
        $users = User::getAll();
        $admin = $users[0];
        $writer = $users[1];
        $commenter = $users[2];

        $posts = $admin->getPosts();
        self::assertCount(2, $posts);
        self::assertContainsOnlyInstancesOf(App\Entities\Post::class, $posts);
        self::assertEmpty(0, $admin->getPages());
        self::assertEmpty(0, $admin->getComments());

        self::assertEmpty(0, $writer->getPosts());
        $pages = $writer->getPages();
        self::assertCount(2, $pages);
        self::assertContainsOnlyInstancesOf(App\Entities\Page::class, $pages);
        $comments = $writer->getComments();
        self::assertCount(1, $comments);
        self::assertContainsOnlyInstancesOf(App\Entities\Comment::class, $comments);

        self::assertEmpty(0, $commenter->getPosts());
        self::assertEmpty(0, $commenter->getPages());
        $comments = $commenter->getComments();
        self::assertCount(2, $comments);
        self::assertContainsOnlyInstancesOf(App\Entities\Comment::class, $comments);
    }

    public function testCreate()
    {
        $newUser = [
            "name" => "FLorent",
            "email" => "flo@rent.fr",
            "role" => "admin",
            "password" => "azerty"
        ];

        $user = User::create($newUser);
        self::assertInstanceOf(User::class, $user);

        $sameUser = User::get(["id" => $user->id]);
        self::assertEquals($user, $sameUser);
        $otherUser = User::get(["id" => 1]);
        self::assertNotEquals($user, $otherUser);

        self::assertNotEmpty($user->email_token);

        self::assertEquals(4, User::countAll());
        self::assertCount(2, User::getAll(["role" => "admin"]));
    }
}
