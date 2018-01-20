<?php

namespace App;

use App\Entities\User;
use PHPMailer;

class Emails
{
    public static function send(string $to, string $subject, string $body): bool
    {
        $formAddress = Config::get("mailer_from_address");
        $formName = Config::get("mailer_from_name");

        if (Config::get("smtp_host", "") === "") {
            $headers = "MIME-Version: 1.0 \n";
            $headers .= "Content-type: text/html; charset=utf-8 \n";
            $headers .= "From: $formAddress <$formName> \n";
            $headers .= "Reply-To: $formAddress \n";

            if (! mail($to, $subject, $body, $headers)) {
                Messages::addError("email.notsent");
                return false;
            }
        } else {
            $mail = new PHPmailer;

            $mail->isSMTP();
            // $mail->SMTPDebug = 3;
            $mail->Host = Config::get("smtp_host");
            $mail->SMTPAuth = true;
            $mail->Username = Config::get("smtp_user");
            $mail->Password = Config::get("smtp_password");
            $mail->SMTPSecure = 'tls';        // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;

            $mail->setFrom($formAddress, $formName);
            $mail->addAddress($to);
            $mail->isHTML(true);

            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);

            if (! $mail->send()) {
                Messages::addError("email.notsent");
                Messages::addError("Mailer Error: " . $mail->ErrorInfo);
                return false;
            }
        }
        return true;
    }

    public static function sendConfirmEmail(User $user): bool
    {
        $subject = Lang::get("email.confirmemail.subject");
        $url = Router::getUrl("register/confirmemail/".$user->id."/".$user->email_token);
        $body = Lang::get("email.confirmemail.body", ["url" => $url]);
        return self::send($user->email, $subject, $body);
    }

    public static function sendChangePassword(User $user): bool
    {
        $subject = Lang::get("email.changepassword.subject");
        $url = Router::getURL("login/resetpassword/".$user->id."/".$user->password_token);
        $body = Lang::get("email.changepassword.body", ["url" => $url]);
        return self::send($user->email, $subject, $body);
    }

    public static function sendTest(string $email): bool
    {
        $subject = Lang::get("email.test.subject");
        $body = Lang::get("email.test.body");
        return self::send($email, $subject, $body);
    }
}
