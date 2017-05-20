<?php

namespace App;

use App\Session;

class Security
{

    public static function getUniqueToken($size = 40)
    {
        // don't use random_bytes() so that it work on PHP5.6 too
        $strong = true;
        $bytes = openssl_random_pseudo_bytes($size / 2, $strong);
        return bin2hex($bytes);
    }

    // ----------------------------------------------

    public static function createCSRFTokens($request)
    {
        $token = self::getUniqueToken();
        Session::set($request."_csrf_token", $token);
        Session::set($request."_csrf_time", time());
        return $token;
    }
}
