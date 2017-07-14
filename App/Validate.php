<?php

namespace App;

class Validate extends Database
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
        $pattern = "/^[a-zA-Z0-9_:,?!\. -]{2,}$/";
        return self::validate($data, $pattern);
    }

    public static function name($data)
    {
        $pattern = "/^[a-zA-Z0-9-]{2,}$/";
        return self::validate($data, $pattern);
    }

    public static function slug($data)
    {
        $pattern = "/^[a-z]{1}[a-z0-9-]{1,}$/";
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
     * @param int $timeLimit The validity duration of a token. Default 900 sec = 15 min
     * @return bool
     */
    public static function csrf($request, $token = null, $timeLimit = 900)
    {
        $tokenName = $request."_csrf_token";

        if ($token === null) {
            if (isset($_POST[$tokenName])) {
                $token = $_POST[$tokenName];
            } else {
                return false;
            }
        }

        if (Session::get($tokenName) === $token &&
            time() < Session::get($request."_csrf_time") + $timeLimit)
        {
            unset($_POST[$tokenName]);
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

                case "checkbox":
                    $value === "on" ? $value = 1 : $value = 0;
                    break;
            }

            $sanitizedPost[$key] = $value; // if $value was null, it is now 0, false or ""
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

        if (! isset($user["id"]) && self::valueExistsInDB($user["name"], "name", "users")) {
            $ok = false;
            Messages::addError("user.namenotunique");
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

        if (isset($user["role"])) {
            $roles = ["admin", "writer", "commenter"];
            if (! in_array($user["role"], $roles)) {
                $ok = false;
                Messages::addError("fieldvalidation.role");
            }
        }

        return $ok;
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function category($data)
    {
        $ok = true;

        if (! self::slug($data["slug"])) {
            $ok = false;
            Messages::addError("fieldvalidation.slug");
        }

        if (! isset($data["id"]) && self::valueExistsInDB($data["slug"], "slug", "categories")) {
            $ok = false;
            Messages::addError("db.slugnotunique");
        }

        if (! self::title($data["title"])) {
            $ok = false;
            Messages::addError("fieldvalidation.title");
        }

        return $ok;
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function post($data)
    {
        $ok = true;

        if (! self::slug($data["slug"])) {
            $ok = false;
            Messages::addError("fieldvalidation.slug");
        }

        if (! isset($data["id"]) && self::valueExistsInDB($data["slug"], "slug", "posts")) {
            $ok = false;
            Messages::addError("db.slugnotunique");
        }

        if (! self::title($data["title"])) {
            $ok = false;
            Messages::addError("fieldvalidation.title");
        }

        $cat = \App\Entities\Category::get($data["category_id"]);
        if ($cat === false) {
            $ok = false;
            Messages::addError("category.unknown");
        }

        $user = \App\Entities\User::get($data["user_id"]);
        if ($user === false) {
            $ok = false;
            Messages::addError("user.unknown");
        }

        return $ok;
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function page($data)
    {
        $ok = true;

        if (! self::slug($data["slug"])) {
            $ok = false;
            Messages::addError("fieldvalidation.slug");
        }

        if (! isset($data["id"]) && self::valueExistsInDB($data["slug"], "slug", "pages")) {
            $ok = false;
            Messages::addError("db.slugnotunique");
        }

        if (! self::title($data["title"])) {
            $ok = false;
            Messages::addError("fieldvalidation.title");
        }

        if (is_int($data["parent_page_id"]) && $data["parent_page_id"] > 0) {
            if (isset($data["id"]) && $data["parent_page_id"] === $data["id"]) {
                $ok = false;
                Messages::addError("page.cantparenttoitself");
            } else {
                $parentPage = \App\Entities\Page::get($data["parent_page_id"]);
                if ($parentPage === false) {
                    $ok = false;
                    Messages::addError("page.unknown");
                }
            }
        }

        return $ok;
    }
}
