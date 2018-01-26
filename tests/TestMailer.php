<?php

namespace Tests;

use App\Mailer;

class TestMailer extends Mailer
{
    public $to = "";
    public $subject = "";
    public $body = "";

    public function send(string $to, string $subject, string $body): bool
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        return true;
    }
}
