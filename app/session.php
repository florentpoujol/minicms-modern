<?php

namespace App;

class Session
{
    public static function getId()
    {
        return session_id();
    }

    public static function get($key, $defaultValue = null)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        else {
            return $defaultValue;
        }
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function destroy($key = null)
    {
        if (isset($key)) {
            if (isset($_SESSION[$key])) {
                $_SESSION[$key] = "";
                unset($_SESSION[$key]);
            }
        }
        else {
            $_SESSION = [];
            unset($_SESSION);
            session_destroy();
        }
    }
}
