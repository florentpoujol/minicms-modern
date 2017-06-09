<?php

namespace App;

class Security
{

    /**
     * @param int $size The size of the returned string. Sometimes the returned string is 1 character shorter.
     * @return string
     */
    public static function getUniqueToken($size = 40)
    {
        $bytes = random_bytes($size / 2);
        return bin2hex($bytes);
    }

    public static function createCSRFTokens($request)
    {
        $token = self::getUniqueToken();
        Session::set($request."_csrf_token", $token);
        Session::set($request."_csrf_time", time());
        return $token;
    }
}
