<?php

namespace App;

use App\Entities\Category;
use App\Entities\Page;
use App\Entities\Post;
use App\Entities\User;

class Validate extends Database
{
    /**
     * check the data against the patterns
     * @param mixed $data
     * @param string|array $patterns can be string or array of strings
     * @return bool true if all pattern(s) are found in the data, false otherwise
     */
    public static function validate($data, $patterns): bool
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

    /**
     * @param mixed $data
     */
    public static function title(string $data): bool
    {
        $pattern = "/^[a-zA-Z0-9_:,?!\. -]{2,}$/";
        return self::validate($data, $pattern);
    }

    /**
     * @param mixed $data
     */
    public static function name(string $data): bool
    {
        $pattern = "/^[a-zA-Z0-9-]{2,}$/";
        return self::validate($data, $pattern);
    }

    /**
     * @param mixed $data
     */
    public static function slug(string $data): bool
    {
        $pattern = "/^[a-z]{1}[a-z0-9-]{1,}$/";
        return self::validate($data, $pattern);
    }

    /**
     * @param mixed $data
     */
    public static function email(string $data): bool
    {
        $pattern = "/^[a-zA-Z0-9_\.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9\.-]+$/";
        return self::validate($data, $pattern);
    }

    /**
     * @param mixed $data
     */
    public static function password(string $data, string $confirm = null): bool
    {
        $patterns = ["/[A-Z]+/", "/[a-z]+/", "/[0-9]+/", "/^.{3,}$/"];
        $formatOK = self::validate($data, $patterns);

        if ($confirm !== null) {
            return ($formatOK && $data === $confirm);
        }
        return $formatOK;
    }

    /**
     * Validate the CSRF token found in session with the one provided with the request
     * @param string $token The token provided with the request. If null, if will be found in $_POST based on the request name
     * @param int $timeLimit The validity duration of a token. Default 900 sec = 15 min
     */
    public static function csrf(string $requestName, string $token = null, int $timeLimit = 900): bool
    {
        $tokenName = $requestName . "_csrf_token";

        if ($token === null) {
            if (isset($_POST[$tokenName])) {
                $token = $_POST[$tokenName];
            } else {
                return false;
            }
        }

        if (Session::get($tokenName) === $token &&
            time() < Session::get($requestName."_csrf_time") + $timeLimit)
        {
            unset($_POST[$tokenName]);
            Session::destroy($tokenName);
            Session::destroy($requestName."_csrf_time");
            return true;
        }

        return false;
    }

    /**
     * Returns an array of only the specified keys from $_POST, casted to their desired types
     * @param array $schema Assoc array containing the desired keys and their wanted type
     */
    public static function sanitizePost(array $schema): array
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
                    $value = (int)($value === "on");
                    break;

                case "array":
                    if (! is_array($value)) {
                        $value = (array)$value;
                    }
                    break;

                default:
                    throw new \UnexpectedValueException("Unhandled type: $type");
                    break;
            }

            $sanitizedPost[$key] = $value; // if $value was null, it is now 0, false or ""
        }

        return $sanitizedPost;
    }

    /**
     * Check for all the user data (name, email, password if any, etc...)
     */
    public static function user(array $user): bool
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

    public static function category(array $data): bool
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

    public static function post(array $data): bool
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

        $cat = Category::get($data["category_id"]);
        if ($cat === false) {
            $ok = false;
            Messages::addError("category.unknown");
        }

        $user = User::get($data["user_id"]);
        if ($user === false) {
            $ok = false;
            Messages::addError("user.unknown");
        }

        return $ok;
    }

    public static function page(array $data): bool
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
                $parentPage = Page::get($data["parent_page_id"]);
                if ($parentPage === false) {
                    $ok = false;
                    Messages::addError("page.unknown");
                }
            }
        }

        $user = User::get($data["user_id"]);
        if ($user === false) {
            $ok = false;
            Messages::addError("user.unknown");
        }

        return $ok;
    }

    public static function menu(array $data): bool
    {
        $ok = true;

        if (! self::name($data["name"])) {
            $ok = false;
            Messages::addError("fieldvalidation.name");
        }

        // check for valid JSON
        if (json_decode($data["json_structure"]) === null) {
            $ok = false;
            Messages::addError("fieldvalidation.menustructure");
        }

        return $ok;
    }

    public static function comment(array $data): bool
    {
        $ok = true;

        $len = strlen($data["content"]);
        if ($len < 10 || $len > 1000) {
            $ok = false;
            Messages::addError("fieldvalidation.commentcontent");
        }

        $user = User::get($data["user_id"]);
        if ($user === false) {
            $ok = false;
            Messages::addError("user.unknown");
        }

        if (isset($data["page_id"])) {
            $page = Page::get($data["page_id"]);
            if ($page === false) {
                $ok = false;
                Messages::addError("page.unknown");
            }
        }

        if (isset($data["post_id"])) {
            $post = Post::get($data["post_id"]);
            if ($post === false) {
                $ok = false;
                Messages::addError("post.unknown");
            }
        }

        return $ok;
    }

    public static function media(array $data): bool
    {
        $ok = true;

        if (! self::name($data["slug"])) {
            $ok = false;
            Messages::addError("fieldvalidation.slug");
        }

        $user = User::get($data["user_id"]);
        if ($user === false) {
            $ok = false;
            Messages::addError("user.unknown");
        }

        return $ok;
    }
}
