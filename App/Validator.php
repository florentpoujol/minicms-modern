<?php

namespace App;

use App\Entities\Category;
use App\Entities\Page;
use App\Entities\Post;
use App\Entities\User;

class Validator extends Database
{
    /**
     * @var Session
     */
    public $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * check the data against the patterns
     * @param mixed $data
     * @param string|array $patterns can be string or array of strings
     * @return bool true if all pattern(s) are found in the data, false otherwise
     */
    public function validate($data, $patterns): bool
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
    public function title(string $data): bool
    {
        $pattern = "/^[a-zA-Z0-9_:,?!\. -]{2,}$/";
        return $this->validate($data, $pattern);
    }

    /**
     * @param mixed $data
     */
    public function name(string $data): bool
    {
        $pattern = "/^[a-zA-Z0-9-]{2,}$/";
        return $this->validate($data, $pattern);
    }

    /**
     * @param mixed $data
     */
    public function slug(string $data): bool
    {
        $pattern = "/^[a-z]{1}[a-z0-9-]{1,}$/";
        return $this->validate($data, $pattern);
    }

    /**
     * @param mixed $data
     */
    public function email(string $data): bool
    {
        $pattern = "/^[a-zA-Z0-9_\.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9\.-]+$/";
        return $this->validate($data, $pattern);
    }

    /**
     * @param mixed $data
     */
    public function password(string $data, string $confirm = null): bool
    {
        $patterns = ["/[A-Z]+/", "/[a-z]+/", "/[0-9]+/", "/^.{3,}$/"];
        $formatOK = $this->validate($data, $patterns);

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
    public function csrf(string $requestName, string $token = null, int $timeLimit = 900): bool
    {
        $tokenName = $requestName . "_csrf_token";

        if ($token === null) {
            if (isset($_POST[$tokenName])) {
                $token = $_POST[$tokenName];
            } else {
                return false;
            }
        }

        if ($this->session->get($tokenName) === $token &&
            time() < $this->session->get($requestName . "_csrf_time") + $timeLimit)
        {
            unset($_POST[$tokenName]);
            $this->session->delete($tokenName);
            $this->session->delete($requestName . "_csrf_time");
            return true;
        }

        return false;
    }

    /**
     * Returns an array of only the specified keys from $_POST, casted to their desired types
     * @param array $schema Assoc array containing the desired keys and their wanted type
     */
    public function sanitizePost(array $schema): array
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
    public function user(array $user): bool
    {
        $ok = true;

        if (! $this->name($user["name"])) {
            $ok = false;
            $this->session->addError("fieldvalidation.name");
        }

        if (! isset($user["id"]) && $this->valueExistsInDB($user["name"], "name", "users")) {
            $ok = false;
            $this->session->addError("user.namenotunique");
        }

        if (! $this->email($user["email"])) {
            $ok = false;
            $this->session->addError("fieldvalidation.email");
        }

        if (isset($user["password"]) && $user["password"] !== "") {
            if (! isset($user["password_confirm"])) {
                $user["password_confirm"] = null;
            }

            if (! Validator::password($user["password"], $user["password_confirm"])) {
                $ok = false;
                $this->session->addError("fieldvalidation.passwordnotequal");
            }
        }

        if (isset($user["role"])) {
            $roles = ["admin", "writer", "commenter"];
            if (! in_array($user["role"], $roles)) {
                $ok = false;
                $this->session->addError("fieldvalidation.role");
            }
        }

        return $ok;
    }

    public function category(array $data): bool
    {
        $ok = true;

        if (! $this->slug($data["slug"])) {
            $ok = false;
            $this->session->addError("fieldvalidation.slug");
        }

        if (! isset($data["id"]) && $this->valueExistsInDB($data["slug"], "slug", "categories")) {
            $ok = false;
            $this->session->addError("db.slugnotunique");
        }

        if (! $this->title($data["title"])) {
            $ok = false;
            $this->session->addError("fieldvalidation.title");
        }

        return $ok;
    }

    public function post(array $data): bool
    {
        $ok = true;

        if (! $this->slug($data["slug"])) {
            $ok = false;
            $this->session->addError("fieldvalidation.slug");
        }

        if (! isset($data["id"]) && $this->valueExistsInDB($data["slug"], "slug", "posts")) {
            $ok = false;
            $this->session->addError("db.slugnotunique");
        }

        if (! $this->title($data["title"])) {
            $ok = false;
            $this->session->addError("fieldvalidation.title");
        }

        $cat = Category::get($data["category_id"]);
        if ($cat === false) {
            $ok = false;
            $this->session->addError("category.unknown");
        }

        $user = User::get($data["user_id"]);
        if ($user === false) {
            $ok = false;
            $this->session->addError("user.unknown");
        }

        return $ok;
    }

    public function page(array $data): bool
    {
        $ok = true;

        if (! $this->slug($data["slug"])) {
            $ok = false;
            $this->session->addError("fieldvalidation.slug");
        }

        if (! isset($data["id"]) && $this->valueExistsInDB($data["slug"], "slug", "pages")) {
            $ok = false;
            $this->session->addError("db.slugnotunique");
        }

        if (! $this->title($data["title"])) {
            $ok = false;
            $this->session->addError("fieldvalidation.title");
        }

        if (is_int($data["parent_page_id"]) && $data["parent_page_id"] > 0) {
            if (isset($data["id"]) && $data["parent_page_id"] === $data["id"]) {
                $ok = false;
                $this->session->addError("page.cantparenttoitself");
            } else {
                $parentPage = Page::get($data["parent_page_id"]);
                if ($parentPage === false) {
                    $ok = false;
                    $this->session->addError("page.unknown");
                }
            }
        }

        $user = User::get($data["user_id"]);
        if ($user === false) {
            $ok = false;
            $this->session->addError("user.unknown");
        }

        return $ok;
    }

    public function menu(array $data): bool
    {
        $ok = true;

        if (! $this->name($data["name"])) {
            $ok = false;
            $this->session->addError("fieldvalidation.name");
        }

        // check for valid JSON
        if (json_decode($data["json_structure"]) === null) {
            $ok = false;
            $this->session->addError("fieldvalidation.menustructure");
        }

        return $ok;
    }

    public function comment(array $data): bool
    {
        $ok = true;

        $len = strlen($data["content"]);
        if ($len < 10 || $len > 1000) {
            $ok = false;
            $this->session->addError("fieldvalidation.commentcontent");
        }

        $user = User::get($data["user_id"]);
        if ($user === false) {
            $ok = false;
            $this->session->addError("user.unknown");
        }

        if (isset($data["page_id"])) {
            $page = Page::get($data["page_id"]);
            if ($page === false) {
                $ok = false;
                $this->session->addError("page.unknown");
            }
        }

        if (isset($data["post_id"])) {
            $post = Post::get($data["post_id"]);
            if ($post === false) {
                $ok = false;
                $this->session->addError("post.unknown");
            }
        }

        return $ok;
    }

    public function media(array $data): bool
    {
        $ok = true;

        if (! $this->name($data["slug"])) {
            $ok = false;
            $this->session->addError("fieldvalidation.slug");
        }

        $user = User::get($data["user_id"]);
        if ($user === false) {
            $ok = false;
            $this->session->addError("user.unknown");
        }

        return $ok;
    }
}
