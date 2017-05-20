<?php

class Emails
{
    public static function send($to, $subject, $body)
    {
        global $smtpHost, $smtpUser, $smtpPassword, $emailFrom;
        $mail = new PHPmailer;

        $mail->isSMTP();
        // $mail->SMTPDebug = 3;
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPassword;
        $mail->SMTPSecure = 'tls';        // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;

        $mail->setFrom($emailFrom, 'MimiCMS MVC Mailer');
        $mail->addAddress($to);
        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        if($mail->send() === false) {
            Messages::addError("Email wasn't sent !");
            Messages::addError("Mailer Error: ".$mail->ErrorInfo);
            return false;
        }

        return true;
    }

    public static function sendConfirmEmail($user)
    {
        $subject = "Confirm your email address";
        $body = "You have registered or changed your email address on the site. <br> Please click the link below to verify the email adress. <br><br>";
        $link = App::$url."index.php?c=register&a=confirmemail&id=".$user->id."&token=".$user->email_token;
        $body .= "<a href='$link'>$link</a>";

        return self::send($user->email, $subject, $body);
    }

    public static function sendChangePassword($user)
    {
        $subject = "Change your password";
        $body = "You have requested to change your password. <br> Click the link below within 48 hours to access the form.<br>";
        $link = App::$url."index.php?c=login&a=resetpassword&id=".$user->id."&token=".$user->password_token;
        $body .= "<a href='$link'>$link</a>";

        return self::send($user->email, $subject, $body);
    }
}
