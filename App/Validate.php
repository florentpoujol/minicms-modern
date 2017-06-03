<?php

namespace App;

class Validate
{
    /**
     * check the data against the patterns
     * @param mixed $data
     * @param string|array $patterns can be string or array of strings
     * @return bool true if all pattern(s) are found in the data, false otherwise
     */
    public static function validate($data, $patterns)
    {
        if (! is_array($patterns)) {
            $patterns = [$patterns];
        }

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $data) !== 1) {
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
        // todo: prevent to begin by number and to contain only number
        $pattern = "/^[a-z0-9-]{2,}$/";
        return self::validate($data, $pattern);
    }

    public static function email($data)
    {
        $pattern = "/^[a-zA-Z0-9_\.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9\.-]+$/";
        return self::validate($data, $pattern);
    }

    public static function password($data, $confirm = null)
    {
        $patterns = ["/[A-Z]+/", "/[a-z]+/", "/[0-9]+/", "/^.{3,}$/"];
        $formatOK = self::validate($data, $patterns);

        if (isset($confirm)) {
            return ($formatOK && $data === $confirm);
        }

        return $formatOK;
    }

    /**
     * Validate the CSRF token found in session with the one provided with the request
     * @param string $request The name of the request
     * @param string $token The token provided with the request. If null, if will be found in $_POST based on the request name
     * @param int $timeLimit Default 900 sec = 15 min
     * @return bool
     */
    public static function csrf($request, $token = null, $timeLimit = 900)
    {
        $tokenName = $request."_csrf_token";

        if (! isset($token)) {
            if (isset($_POST[$tokenName])) {
                $token = $_POST[$tokenName];
            } else {
                return false;
            }
        }

        if (
            Session::get($tokenName) === $token &&
            time() < Session::get($request."_csrf_time", 0) + $timeLimit
        ) {
            Session::destroy($tokenName);
            Session::destroy($request."_csrf_time");
            return true;
        }

        return false;
    }

    /**
     * Returns an array of only the specified keys from $_POST, casted to their desired types
     * @param array $schema Assoc array containing the desired key and their wanted type
     * @return array
     */
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

    /**
     * Check for all the user data (name, email, password if any, etc...)
     * @param array $user
     * @return bool
     */
    public static function user($user)
    {
        $ok = true;

        if (! self::name($user["name"])) {
            $ok = false;
            Messages::addError("fieldvalidation.name");
        }

        if (! self::email($user["email"])) {
            $ok = false;
            Messages::addError("fieldvalidation.email");
        }

        if (isset($user["password"]) && $user["password"] !== "") {
            if (! isset($user["password_confirm"])) {
                $user["password_confirm"] = null;
            }

            if (! Validate::password($user["password"], $user["password_confirm"])) {
                $ok = false;
                Messages::addError("fieldvalidation.passwordnotequal");
            }
        }

        /*if (isset($user["role"])) {
            $roles = ["admin", "writer", "commenter"];
            if (! in_array($user["role"], $roles)) {
                addError("Role must be 'commenter', 'writer' or 'admin'.");
                $userOK = false;
            }
        }*/

        return $ok;
    }
}
