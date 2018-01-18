<?php

namespace App;

class Security
{

    /**
     * @param int $size The size of the returned string. Sometimes the returned string is 1 character shorter.
     * @return string
     */
    public static function getUniqueToken(int $size = 40): string
    {
        $bytes = random_bytes($size / 2);
        return bin2hex($bytes);
    }

    public static function createCSRFTokens(string $requestName): string
    {
        $token = self::getUniqueToken();
        Session::set($requestName . "_csrf_token", $token);
        Session::set($requestName . "_csrf_time", time());
        return $token;
    }
}
