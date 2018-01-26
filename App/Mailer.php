<?php

namespace App;

use App\Entities\User;
use PHPMailer;

class Mailer
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Lang
     */
    protected $lang;

    /**
     * @var Router
     */
    protected $router;

    public function __construct(Config $config, Session $session, Lang $lang, Router $router)
    {
        $this->config = $config;
        $this->session = $session;
        $this->lang = $lang;
        $this->router = $router;
    }

    public function send(string $to, string $subject, string $body): bool
    {
        $formAddress = $this->config->get("mailer_from_address");
        $formName = $this->config->get("mailer_from_name");

        if ($this->config->get("smtp_host", "") === "") {
            $headers = "MIME-Version: 1.0 \n";
            $headers .= "Content-type: text/html; charset=utf-8 \n";
            $headers .= "From: $formAddress <$formName> \n";
            $headers .= "Reply-To: $formAddress \n";

            if (! mail($to, $subject, $body, $headers)) {
                $this->session->addError("email.notsent");
                return false;
            }
        } else {
            $mailer = new PHPMailer();

            $mailer->isSMTP();
            // $mailer->SMTPDebug = 3;
            $mailer->SMTPAuth = true;
            $mailer->SMTPSecure = 'tls';        // Enable TLS encryption, `ssl` also accepted
            $mailer->Port = 587;
            $mailer->Host = $this->config->get("smtp_host");
            $mailer->Username = $this->config->get("smtp_user");
            $mailer->Password = $this->config->get("smtp_password");

            $mailer->setFrom($formAddress, $formName);
            $mailer->addAddress($to);
            $mailer->isHTML(true);

            $mailer->Subject = $subject;
            $mailer->Body = $body;
            $mailer->AltBody = strip_tags($body);

            if (! $mailer->send()) {
                $this->session->addError("email.notsent");
                $this->session->addError("Mailer Error: " . $mailer->ErrorInfo);
                return false;
            }
        }
        return true;
    }

    public function sendConfirmEmail(User $user): bool
    {
        $subject = $this->lang->get("email.confirmemail.subject");
        $url = $this->router->getUrl("register/confirmemail/$user->id/$user->email_token");
        $body = $this->lang->get("email.confirmemail.body", ["url" => $url]);
        return $this->send($user->email, $subject, $body);
    }

    public function sendChangePassword(User $user): bool
    {
        $subject = $this->lang->get("email.changepassword.subject");
        $url = $this->router->getURL("login/resetpassword/$user->id/$user->password_token");
        $body = $this->lang->get("email.changepassword.body", ["url" => $url]);
        return $this->send($user->email, $subject, $body);
    }

    public function sendTest(string $email): bool
    {
        $subject = $this->lang->get("email.test.subject");
        $body = $this->lang->get("email.test.body");
        return $this->send($email, $subject, $body);
    }
}
