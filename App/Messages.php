<?php

namespace App;

/**
 * Class Messages
 * Messages are pieces of strings that are to be displyed to the user on a page to notify him of a success or an error
 * Are stored in database, segregated based on session id, when the page needs to be reloaded before displaying the messages.
 * Integrates with the language system. Actual messages can be stored in the "messages.success" or "messages.error" dictionaries.
 * @package App
 */
class Messages extends Database
{
    // arrays of strings
    private static $successes = [];
    private static $errors = [];

    /**
     * @param string $msg Either the message or a corresponding language keys (automatically prefixed with "messages.success")
     * @param array $replacements Keys/values to be replaced in the message
     */
    public static function addSuccess(string $msg, array $replacements = null)
    {
        $msg = Lang::get("messages.success.$msg", $replacements);
        $msg = preg_replace("/^messages\.success\./", "", $msg); // in case the msg isn't found
        self::$successes[] = $msg;
    }

    /**
     * @param string $msg Either the message or a corresponding language keys (automatically prefixed with "messages.success")
     * @param array $replacements Keys/values to be replaced in the message
     */
    public static function addError(string $msg, array $replacements = null)
    {
        $msg = Lang::get("messages.error.$msg", $replacements);
        $msg = preg_replace("/^messages\.error\./", "", $msg);
        self::$errors[] = $msg;
    }

    public static function getSuccesses(): array
    {
        $temp = self::$successes;
        self::$successes = [];
        return $temp;
    }

    public static function getErrors(): array
    {
        $temp = self::$errors;
        self::$errors = [];
        return $temp;
    }

    // save leftover msg in database for retrival later
    public static function save()
    {
        $query = self::$db->prepare("INSERT INTO messages(type, content, time, session_id) VALUES(:type, :content, :time, :session_id)");
        $params = [
            "type" => "success",
            "time" => time(),
            "session_id" => Session::getId()
        ];

        foreach (self::$successes as $msg) {
            $params["content"] = $msg;
            $query->execute($params);
        }
        self::$successes = [];

        $params["type"] = "error";
        foreach (self::$errors as $msg) {
            $params["content"] = $msg;
            $query->execute($params);
        }
        self::$errors = [];
    }

    // retrieve msg from DB, if any
    public static function load()
    {
        $query = self::$db->prepare("SELECT * FROM messages WHERE session_id=?");
        $sessionId = Session::getId();
        $success = $query->execute([$sessionId]);

        if ($success) {
            while ($msg = $query->fetch()) {
                if ($msg->type === "success") {
                    self::$successes[] = $msg->content;
                } else if ($msg->type === "error") {
                    self::$errors[] = $msg->content;
                }
            }

            $query = self::$db->prepare("DELETE FROM messages WHERE session_id=?");
            $query->execute([$sessionId]);
        }

        // delete all leftover messages older than a day
        $query = self::$db->prepare("DELETE FROM messages WHERE time < ?");
        $query->execute([time() - (3600*24)]);
    }
}
