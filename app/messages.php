<?php

namespace App;

class Messages extends Models\Model
{
    // arrays of strings
    private static $successes = [];
    private static $errors = [];

    // Array that holds correspondance between key strings and the actual success or error message
    // values beetween brackets can be replaced by other values with getString()
    private static $messages = [
        "success" => [
            "user" => [
                "loggedin" => "Welcome {userName}, you are now logged in !",
                "unknow" => "You are now logged in !",
            ]
        ],
        "error" => [],
    ];

    // get the actual message that match the key(s) provided
    // ie: "success.user.loggedin"
    // values beetween brackets can be replaced by other values provided in $params
    // ie: ["userName" => "John Doe"]
    private static function getMessage($originalKey, $params = null)
    {
        $string = self::$messages;
        $keys = explode(".", $originalKey);

        foreach ($keys as $key) {
            if (isset($string[$key])) {
                $string = $string[$key];
            }
            else {
                break;
            }
        }

        if (is_array($string)) {
            // the key does not lead to a string value, just return the original $key
            $string = $originalKey;
        }

        // string is now an actual string, process replacement is $params is set
        if (isset($params)) {
            foreach ($params as $key => $value) {
                $string = str_replace("{$key}", $value, $string);
            }
        }

        return $string;
    }


    public static function addSuccess($msg, $params = null)
    {
        $msg = self::getMessage("success.$msg", $params);
        $msg = trim($msg, "success.");
        self::$successes[] = $msg;
    }

    public static function addError($msg, $params = null)
    {
        $msg = self::getMessage("error.$msg", $params);
        $msg = trim($msg, "error.");
        self::$errors[] = $msg;
    }


    public static function getSuccesses()
    {
        $temp = self::$successes;
        self::$successes = [];
        return $temp;
    }

    public static function getErrors()
    {
        $temp = self::$errors;
        self::$errors = [];
        return $temp;
    }


    // save leftover msg in database for retrival later
    public static function save() {
        $query = self::$db->prepare("INSERT INTO messages(type, text, session_id) VALUES(:type, :text, :session_id)");
        $params = [
            "type" => "success",
            "session_id" => Session::getId()
        ];

        foreach (self::$successes as $msg) {
            $params["text"] = $msg;
            $query->execute($params);
        }

        $params["type"] = "error";
        foreach (self::$errors as $msg) {
            $params["text"] = $msg;
            $query->execute($params);
        }
    }


    // retrieve msg from DB, if any
    public static function load() {
        $query = self::$db->prepare("SELECT * FROM messages WHERE session_id=?");
        $sessionId = Session::getId();
        $success = $query->execute([$sessionId]);

        if ($success) {
            while ($msg = $query->fetch()) {
                if ($msg->type === "success") {
                    self::$successes[] = $msg->text;
                }
                else if ($msg->type === "error") {
                    self::$errors[] = $msg->text;
                }
            }

            $query = self::$db->prepare("DELETE FROM messages WHERE session_id=?");
            $query->execute([$sessionId]);
        }
    }
}
