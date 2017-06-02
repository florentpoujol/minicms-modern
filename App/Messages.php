<?php

namespace App;

class Messages extends Models\Model
{
    // arrays of strings
    private static $successes = [];
    private static $errors = [];


    public static function addSuccess($msg, $params = null)
    {
        $msg = Lang::get("messages.success.$msg", $params);
        $msg = trim($msg, "messages.success."); // in case the msg isn't found
        self::$successes[] = $msg;
    }

    public static function addError($msg, $params = null)
    {
        $msg = Lang::get("messages.error.$msg", $params);
        $msg = trim($msg, "messages.error.");
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
