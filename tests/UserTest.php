<?php

use App\Entities\User;
use App\Entities\Comment;

class UserTest extends DatabaseTestCase
{
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
        self::assertEmpty(0, $admin->getComments());

        self::assertEmpty(0, $writer->getPosts());

        $comments = $writer->getComments();
        self::assertCount(1, $comments);
        self::assertContainsOnlyInstancesOf(App\Entities\Comment::class, $comments);

        self::assertEmpty(0, $commenter->getPosts());
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

    public function testUpdate()
    {
        $newUser = [
            "name" => "FLorent",
            "email" => "flo@rent.fr",
            "role" => "admin",
            "password" => "azerty"
        ];

        $user = User::create($newUser);
        self::assertNotEmpty($user->email_token);

        self::assertTrue($user->updateEmailToken(""));
        self::assertEmpty($user->email_token);

        self::assertEmpty($user->password_token);
        self::assertEquals(0, $user->password_change_time);
        self::assertTrue($user->updatePasswordToken("newtoken"));
        self::assertEquals("newtoken", $user->password_token);
        $time = time();
        self::assertGreaterThan($time - 1, $user->password_change_time);
        self::assertLessThan($time + 1, $user->password_change_time);

        self::assertTrue(password_verify("azerty", $user->password_hash));
        self::assertTrue($user->updatePassword("qwerty"));
        self::assertTrue(password_verify("qwerty", $user->password_hash));
        self::assertEmpty($user->password_token);
        self::assertEquals(0, $user->password_change_time);

        self::assertEquals(0, $user->is_blocked);
        self::assertFalse($user->isBlocked());
        self::assertTrue($user->block());
        self::assertEquals(1, $user->is_blocked);
        self::assertTrue($user->isBlocked());
        self::assertTrue($user->block(false));
        self::assertEquals(0, $user->is_blocked);
        self::assertFalse($user->isBlocked());
    }

    public function testDelete()
    {
        $user = User::get(["id" => 2]); // the writer, who has 1 comment and 2 pages

        self::assertCount(1, Comment::getAll(["user_id" => 2]));
        self::assertEquals(3, Comment::countAll());

        $user->deleteByAdmin(1);

        self::assertFalse(User::get(["id" => 2]));

        self::assertCount(0, Comment::getAll(["user_id" => 2]));
        self::assertEquals(2, Comment::countAll());

        self::assertNull($user->id);
        self::assertNull($user->name);
    }
}
