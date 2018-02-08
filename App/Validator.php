<?php

namespace App;

class Validator
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(Session $session, Database $database, Config $config)
    {
        $this->session = $session;
        $this->database = $database;
        $this->config = $config;
    }

    /**
     * Check the data against the provided patterns
     * @param mixed $data
     * @param string|array $patterns can be string or array of strings
     * @return bool True if all pattern(s) are found in the data, false otherwise
     */
    public function validate($data, $patterns): bool
    {
        if (!is_array($patterns)) {
            $patterns = [$patterns];
        }

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $data) !== 1) {
                return false;
            }
        }
        return true;
    }

    public function title(string $data): bool
    {
        $pattern = "/^[a-zA-Z0-9_:,?!\. -]{2,}$/";
        return $this->validate($data, $pattern);
    }

    public function name(string $data): bool
    {
        $pattern = "/^[a-zA-Z0-9-]{2,}$/";
        return $this->validate($data, $pattern);
    }

    public function slug(string $data): bool
    {
        $pattern = "/^[a-z]{1}[a-z0-9-]{1,}$/";
        return $this->validate($data, $pattern);
    }

    public function email(string $data): bool
    {
        $pattern = "/^[a-zA-Z0-9_\.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9\.-]+$/";
        return $this->validate($data, $pattern);
    }

    public function password(string $password, string $confirm = null): bool
    {
        if ($confirm !== null) {
            return $password === $confirm;
        }

        $patterns = ["/[A-Z]+/", "/[a-z]+/", "/[0-9]+/", "/^.{3,}$/"];
        return $this->validate($password, $patterns);
    }

    /**
     * Validate the CSRF token found in session with the one provided with the request
     * @param string $token The token provided with the request. If null, if will be found in $_POST based on the request name
     * @param int $timeLimit The validity duration of a token. Default 900 sec = 15 min
     */
    public function csrf(string $requestName, string $token = null, int $timeLimit = 900): bool
    {
        $tokenKey = $requestName . "_csrf_token";
        $timeKey = $requestName . "_csrf_time";

        if ($token === null) {
            if (!isset($_POST[$tokenKey])) {
                return false;
            }
            $token = $_POST[$tokenKey];
        }

        if ($this->session->get($tokenKey) === $token &&
            time() < $this->session->get($timeKey) + $timeLimit)
        {
            unset($_POST[$tokenKey]);
            $this->session->delete($tokenKey);
            $this->session->delete($timeKey);
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
                    $value = (int)$value;
                    break;

                case "bool":
                    $value = (bool)$value;
                    break;

                case "string":
                    $value = (string)$value;
                    break;

                case "checkbox":
                    $value = (int)($value === "on"); // when checkbox is not checked, $value will actually be null ($key not present in $_POST)
                    break;

                case "array":
                    $value = (array)$value;
                    break;

                default:
                    throw new \UnexpectedValueException("Unhandled type: '$type'.");
                    break;
            }

            $sanitizedPost[$key] = $value; // if $value was null, it is now 0, false, "" or []
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

        if (! isset($user["id"]) && $this->database->valueExistsInDB($user["name"], "name", "users")) {
            $ok = false;
            $this->session->addError("user.namenotunique");
        }

        if (! $this->email($user["email"])) {
            $ok = false;
            $this->session->addError("fieldvalidation.email");
        }

        if (isset($user["password"]) && $user["password"] !== "") {
            if (! isset($user["password_confirmation"])) {
                $user["password_confirmation"] = null;
            }

            if (! $this->password($user["password"])) {
                $ok = false;
                $this->session->addError("fieldvalidation.password");
            }

            if (! $this->password($user["password"], $user["password_confirmation"])) {
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

        if (! isset($data["id"]) && $this->database->valueExistsInDB($data["slug"], "slug", "categories")) {
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

        if (! isset($data["id"]) && $this->database->valueExistsInDB($data["slug"], "slug", "posts")) {
            $ok = false;
            $this->session->addError("db.slugnotunique");
        }

        if (! $this->title($data["title"])) {
            $ok = false;
            $this->session->addError("fieldvalidation.title");
        }

        if (! $this->database->valueExistsInDB($data["category_id"], "id", "categories")) {
            $ok = false;
            $this->session->addError("category.unknown");
        }

        if (! $this->database->valueExistsInDB($data["user_id"], "id", "users")) {
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

        if (! isset($data["id"]) && $this->database->valueExistsInDB($data["slug"], "slug", "pages")) {
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
                if (! $this->database->valueExistsInDB($data["parent_page_id"], "id", "pages")) {
                    $ok = false;
                    $this->session->addError("page.unknown");
                }
            }
        }

        if (! $this->database->valueExistsInDB($data["user_id"], "id", "users")) {
            $ok = false;
            $this->session->addError("user.unknown");
        }

        return $ok;
    }

    public function menu(array $data): bool
    {
        $ok = true;

        if (! $this->title($data["title"])) {
            $ok = false;
            $this->session->addError("fieldvalidation.title");
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

        if (! $this->database->valueExistsInDB($data["user_id"], "id", "users")) {
            $ok = false;
            $this->session->addError("user.unknown");
        }

        if (isset($data["page_id"])) {
            if (! $this->database->valueExistsInDB($data["page_id"], "id", "pages")) {
                $ok = false;
                $this->session->addError("page.unknown");
            }
        }

        if (isset($data["post_id"])) {
            if (! $this->database->valueExistsInDB($data["post_id"], "id", "posts")) {
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

        if (! $this->database->valueExistsInDB($data["user_id"], "id", "users")) {
            $ok = false;
            $this->session->addError("user.unknown");
        }

        return $ok;
    }

    public function recaptcha(): bool
    {
        $secret = $this->config->get("recaptcha_secret", "");
        if ($secret !== "") {
            $postFields = [
                "secret" => $secret,
                "response" => $_POST["g-recaptcha-response"] ?? "no_response",
            ];
            $postFields = http_build_query($postFields);

            $url = "https://www.google.com/recaptcha/api/siteverify";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields); // gives me an array to string conversion error when not using http_build_query()
            $response = curl_exec($curl);
            curl_close($curl);

            if (is_string($response)) {
                $response = json_decode($response);
                $response = $response->success;
            }
            return $response;
        }
        return true;
    }
}
