<?php

namespace App;

class Validate
{
    // check the data agains the patterns
    // returns true if all pattern(s) are found in the data, false otherwise
    // patterns can be string or array of strings
    public static function validate($data, $patterns)
    {
        if (! is_array($patterns)) {
            $patterns = [$patterns];
        }

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $subject) !== 1) {
                return false;
            }
        }

        return true;
    }


    public static function title($data)
    {
        $pattern = "/^[a-zA-Z0-9_:,?!\.-]{2,}$/";
        return self::validate($data, $pattern);
    }

    public static function name($data)
    {
        $pattern = "/^[a-zA-Z0-9-]{2,}$/";
        return self::validate($data, $pattern);
    }

    public static function slug($data)
    {
        $pattern = "/^[a-z0-9-]{2,}$/";
        return self::validate(strtolowwer($data), $pattern);
    }

    public static function email($data)
    {
        $pattern = "/^[a-zA-Z0-9_\.+-]{1,}@[a-zA-Z0-9-_\.]{3,}$/";
        return self::validate($data, $pattern);
    }

    public static function password($data, $confirm)
    {
        $patterns = ["/[A-Z]+/", "/[a-z]+/", "/[0-9]+/", "/^.{3,}$/"];
        $formatOK = self::validate($data, $pattern);

        if (isset($confirm)) {
            return ($formatOK && $data === $confirm);
        }

        return $formatOK;
    }


    public static function csrf($request, $token, $timeLimit = 900)
    {
        // 900 sec = 15 min
        if (
            Session::get($request."_csrf_token") === $token &&
            time() < Session::get($request."_csrf_time", 0) + $timeLimit
        ) {
            Session::destroy($request."_csrf_token");
            Session::destroy($request."_csrf_time");
            return true;
        }

        return false;
    }

    // return an array on only the specified keys from $_POST, casted to their desired types
    public static function sanitizePost($schema)
    {
        $sanitizedPost = [];

        foreach ($schema as $key => $type) {
            $value = null;
            if (isset($_POST[$key])) {
                $value = $_POST[$key];
            }

            switch ($type) {
                case "int":
                    if (! is_int($value)) {
                        $value = (int)$value;
                    }
                    break;

                case "bool":
                    if (! is_bool($value)) {
                        $value = (bool)$value;
                    }
                    break;

                case "string":
                    if (! is_string($value)) {
                        $value = strval($value);
                    }
                    break;
            }

            $sanitizedPost[$key] = $value;
        }

        return $sanitizedPost;
    }
}
