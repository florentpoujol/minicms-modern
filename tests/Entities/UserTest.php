<?php

namespace Tests;

use App\Entities\Post;
use App\Entities\User;
use App\Entities\Comment;

class UserTest extends DatabaseTestCase
{
    public function testGet()
    {
        $user = $this->userRepo->get(["id" => 999]);
        self::assertInternalType("bool", $user);
        self::assertEquals(false, $user);

        $user = $this->userRepo->get(["id" => 1]);
        self::assertInstanceOf(User::class, $user);
        self::assertEquals(true, $user->isAdmin());
        self::assertEquals(false, $user->isWriter());

        $user = $this->userRepo->get(["role" => "writer"]);
        self::assertInstanceOf(User::class, $user);
        self::assertEquals(true, $user->isWriter());
        self::assertEquals(false, $user->isCommenter());

        $user = $this->userRepo->get(["id" => 2, "role" => "commenter"]);
        self::assertEquals(false, $user);

        $user = $this->userRepo->get(["id" => 3, "role" => "commenter"]); // condition == AND
        self::assertInstanceOf(User::class, $user);
        self::assertEquals(true, $user->isCommenter());
    }

    public function testGetAll()
    {
        $users = $this->userRepo->getAll(["role" => "whatever"]);
        self::assertInternalType("array", $users);
        self::assertEmpty($users);

        $users = $this->userRepo->getAll();
        self::assertCount(3, $users);
        self::assertContainsOnlyInstancesOf(User::class, $users);

        $users = $this->userRepo->getAll(["role" => "writer"]);
        self::assertCount(1, $users);
        self::assertContainsOnlyInstancesOf(User::class, $users);
    }

    public function testCountAll()
    {
        self::assertEquals(3, $this->userRepo->countAll());
    }

    public function testGetResources()
    {
        $users = $this->userRepo->getAll();
        $admin = $users[0];
        $writer = $users[1];
        $commenter = $users[2];

        $posts = $admin->getPosts();
        self::assertCount(1, $posts);
        self::assertContainsOnlyInstancesOf(Post::class, $posts);
        self::assertEmpty(0, $admin->getComments());

        self::assertEmpty(0, $writer->getPosts());

        $comments = $writer->getComments();
        self::assertCount(1, $comments);
        self::assertContainsOnlyInstancesOf(Comment::class, $comments);

        self::assertEmpty(0, $commenter->getPosts());
        $comments = $commenter->getComments();
        self::assertCount(2, $comments);
        self::assertContainsOnlyInstancesOf(Comment::class, $comments);
    }

    public function testCreate()
    {
        $newUser = [
            "name" => "FLorent",
            "email" => "flo@rent.fr",
            "role" => "admin",
            "password" => "azerty"
        ];

        $user = $this->userRepo->create($newUser);
        self::assertInstanceOf(User::class, $user);

        $sameUser = $this->userRepo->get(["id" => $user->id]);
        self::assertEquals($user, $sameUser);
        $otherUser = $this->userRepo->get(["id" => 1]);
        self::assertNotEquals($user, $otherUser);

        self::assertNotEmpty($user->email_token);

        self::assertEquals(4, $this->userRepo->countAll());
        self::assertCount(2, $this->userRepo->getAll(["role" => "admin"]));
    }

    public function testUpdate()
    {
        $newUser = [
            "name" => "FLorent",
            "email" => "flo@rent.fr",
            "role" => "admin",
            "password" => "azerty"
        ];

        $user = $this->userRepo->create($newUser);
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
        $user = $this->userRepo->get(["id" => 2]); // the writer, who has 1 comment and 2 pages

        self::assertCount(1, $this->commentRepo->getAll(["user_id" => 2]));
        self::assertEquals(3, $this->commentRepo->countAll());

        $this->expectException(\LogicException::class);
        $user->delete();

        $user->deleteByAdmin(1);

        self::assertFalse($this->userRepo->get(["id" => 2]));

        self::assertCount(0, $this->commentRepo->getAll(["user_id" => 2]));
        self::assertEquals(2, $this->commentRepo->countAll());
    }
}
