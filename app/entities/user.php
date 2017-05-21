<?php

namespace App\Entities;


/**
 *
 */
class User
{
    /**
     * @var \PDO
     */
    private $dbUser;

    public function __construct($dbUser)
    {
        $this->dbUser = $dbUser;
    }

    public function __get($name)
    {
        return $this->dbUser->{$name};
    }


    public function isAdmin()
    {
        return ($this->role === "admin");
    }

    public function isWriter()
    {
        return ($this->role === "writer");
    }

    public function isCommenter()
    {
        return ($this->role === "commenter");
    }

    // return true is user has one of the roles
    public function hasRoles($role1, $role2 = null)
    {
        $hasRole = ($this->role === $role1);

        if (! $hasRole && isset($role2)) {
            $hasRole = ($hasRole || ($this->role === $role2));
        }

        return $hasRole;
    }
}
