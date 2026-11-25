<?php

declare(strict_types=1);

namespace App;

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

final class Mailer
{
    public static function send(string $to, string $toName, string $subject, string $template, array $data, ?string $replyTo = null): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->Encoding = PHPMailer::ENCODING_QUOTED_PRINTABLE;
            $mail->setFrom((string) App::env('MAIL_FROM'), (string) App::env('MAIL_FROM_NAME', Business::NAME));
            $mail->addAddress($to, self::clean($toName));
            if ($replyTo !== null) {
                $mail->addReplyTo($replyTo);
            }
            $mail->Subject = self::clean($subject);
            $mail->isHTML(true);
            $mail->Body = View::fetch('emails/' . $template, $data + ['subject' => $subject]);
            $mail->AltBody = View::fetch('emails/' . $template . '-text', $data);

            if (App::env('MAIL_DRIVER', 'log') === 'smtp') {
                $mail->isSMTP();
                $mail->Host = (string) App::env('MAIL_HOST');
                $mail->Port = (int) App::env('MAIL_PORT', 587);
                $mail->SMTPAuth = true;
                $mail->Username = (string) App::env('MAIL_USER');
                $mail->Password = (string) App::env('MAIL_PASS');
                $mail->SMTPSecure = App::env('MAIL_ENCRYPTION', 'tls') === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Timeout = 10;

                return $mail->send();
            }

            $mail->preSend();
            $file = sprintf('%s/storage/mail/%s-%s.eml', App::root(), date('Ymd-His'), bin2hex(random_bytes(4)));

            return file_put_contents($file, $mail->getSentMIMEMessage()) !== false;
        } catch (MailException $e) {
            error_log('Mail failed: ' . $mail->ErrorInfo);

            return false;
        }
    }

    private static function clean(string $value): string
    {
        return trim((string) preg_replace('/[\r\n\t]+/', ' ', $value));
    }
}
