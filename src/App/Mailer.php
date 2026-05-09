<?php
declare(strict_types=1);

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    /**
     * Sends an HTML email using the configured SMTP settings in .env.
     * Fallback to native mail() if SMTP is not configured.
     *
     * @param string|array $to Email address(es) to send to.
     * @param string $subject The email subject.
     * @param string $htmlBody The HTML email body.
     * @param string $altBody (Optional) Plain text alternative body.
     * @param string|null $replyToEmail (Optional) Email address to reply to.
     * @param string|null $replyToName (Optional) Name to reply to.
     * @return bool True on success, false on failure.
     */
    public static function send($to, string $subject, string $htmlBody, string $altBody = '', ?string $replyToEmail = null, ?string $replyToName = null): bool
    {
        $mail = new PHPMailer(true);

        try {
            $useSmtp = filter_var(Env::get('SMTP_ENABLED', true), FILTER_VALIDATE_BOOLEAN);

            if ($useSmtp) {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = Env::get('SMTP_HOST', 'smtp.example.com');
                $mail->SMTPAuth   = true;
                $mail->Username   = Env::get('SMTP_USER', 'user@example.com');
                $mail->Password   = Env::get('SMTP_PASS', 'secret');
                $mail->SMTPSecure = Env::get('SMTP_SECURE', PHPMailer::ENCRYPTION_STARTTLS);
                $mail->Port       = (int)Env::get('SMTP_PORT', 587);
            } else {
                $mail->isMail();
            }

            // Sender
            $fromEmail = Env::get('SMTP_FROM_EMAIL', 'noreply@graciouscharity.org');
            $fromName  = Env::get('SMTP_FROM_NAME', Helpers::brandName() . ' Notifications');
            $mail->setFrom($fromEmail, $fromName);

            // Recipients
            if (is_array($to)) {
                foreach ($to as $address) {
                    $mail->addAddress($address);
                }
            } else {
                $mail->addAddress($to);
            }

            // Reply-To
            if ($replyToEmail) {
                $mail->addReplyTo($replyToEmail, $replyToName ?? '');
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $altBody ?: strip_tags(str_replace(['<br>', '<br/>', '</p>'], "\n", $htmlBody));

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
