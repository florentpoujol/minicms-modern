<?php

namespace App;

class Validator
{

    public static function patterns($patterns, $subject)
    {
        if (is_array($patterns) === false) {
            $patterns = [$patterns];
        }

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $subject) == false) {
                // keep loose comparison !
                // preg_match() returns 0 if pattern isn't found, or false on error
                return false;
            }
        }

        return true;
    }


    public static function name($name)
    {
        $namePattern = "^[a-zA-Z0-9_-]{4,}$";
        if (self::patterns("/$namePattern/", $name) === false) {
            Messages::addError("The user name has the wrong format. Minimum four letters, numbers, hyphens or underscores.");
            return false;
        }

        return true;
    }


    public static function email($email)
    {
        $emailPattern = "^[a-zA-Z0-9_\.+-]{1,}@[a-zA-Z0-9-_\.]{4,}$";
        if (self::patterns("/$emailPattern/", $email) === false) {
            Messages::addError("The email has the wrong format");
            return false;
        }

        return true;
    }


    public static function password($password, $passwordConfirm)
    {
        $ok = true;
        $patterns = ["/[A-Z]+/", "/[a-z]+/", "/[0-9]+/"];
        $minPasswordLength = 3;

        if (self::patterns($patterns, $password) === false || strlen($password) < $minPasswordLength) {
            Messages::addError("The password must be at least $minPasswordLength characters long and have at least one lowercase letter, one uppercase letter and one number.");
            $ok = false;
        }

        if (isset($passwordConfirm) === true && $password !== $passwordConfirm) {
            Messages::addError("The password confirmation does not match the password.");
            $ok = false;
        }

        return $ok;
    }

    public static function newUser($newUser) {
        $formatOK = false;
        if (
            self::name($newUser["name"]) === true &&
            self::email($newUser["email"]) === true &&
            self::password($newUser["password"], $newUser["password_confirm"]) === true
        ) {
            $formatOK = true;
        }

        // check that the name doesn't already exist
        unset($newUser["password"]);
        unset($newUser["password_confirm"]);
        $user = Users::get($newUser, "OR");
        if ($user !== false) {
            Messages::addError("Such user already exists");
        }

        return ($formatOK === true && $user === false);
    }
}