<?php

namespace App;

use StdCmp\Session\NativeSession;

class Session extends NativeSession
{
    /**
     * @var Helpers
     */
    public $helpers;

    /**
     * @var Lang
     */
    public $lang;

    function __construct(Helpers $helpers, Lang $lang)
    {
        $this->helpers = $helpers;
        $this->lang = $lang;
        // todo: only store the msg key in the session, not the whole message
    }

    public function
    createCSRFToken(string $requestName): string
    {
        $token = $this->helpers->getUniqueToken();
        $this->set($requestName . "_csrf_token", $token);
        $this->set($requestName . "_csrf_time", time());
        return $token;
    }

    /**
     * @param string $msg Either the message or a corresponding language keys (automatically prefixed with "messages.success")
     * @param array $replacements Keys/values to be replaced in the message
     */
    public function addSuccess(string $msg, array $replacements = null)
    {
        $msg = $this->lang->get("messages.success.$msg", $replacements);
        $msg = preg_replace("/^messages\.success\./", "", $msg); // in case the msg isn't found
        $this->addFlashMessage("flashSuccesses", $msg);
    }

    /**
     * @param string $msg Either the message or a corresponding language keys (automatically prefixed with "messages.success")
     * @param array $replacements Keys/values to be replaced in the message
     */
    public function addError(string $msg, array $replacements = null)
    {
        $msg = $this->lang->get("messages.error.$msg", $replacements);
        $msg = preg_replace("/^messages\.error\./", "", $msg);
        $this->addFlashMessage("flashErrors", $msg);
    }

    public function getSuccesses(): array
    {
        return $this->getFlashMessages("flashSuccesses");
    }

    public function getErrors(): array
    {
        return $this->getFlashMessages("flashErrors");
    }
}
