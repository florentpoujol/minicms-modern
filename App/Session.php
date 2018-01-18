<?php

namespace App;

class Session
{
    public static function getId()
    {
        return session_id();
    }

    /**
     * @param mixed|null $defaultValue
     * @return mixed
     */
    public static function get(string $key, $defaultValue = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
    }

    /**
     * @param mixed $value
     */
    public static function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Deletes a single session value when its key is specified, or destroy the whole session.
     * @param string $key
     */
    public static function destroy(string $key = null)
    {
        if ($key === null) {
            $_SESSION = [];
            unset($_SESSION);
            session_destroy();
        } elseif (isset($_SESSION[$key])) {
            $_SESSION[$key] = null;
            unset($_SESSION[$key]);
        }
    }
}
