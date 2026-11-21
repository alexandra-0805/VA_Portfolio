<?php
/**
 * Outgoing email through SMTP (PHPMailer). Loaded only by pages that send mail.
 */

require_once ROOT_PATH . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

function mail_is_configured(): bool
{
    return config('mail.username') !== '' && config('mail.password') !== '';
}

/**
 * Send a plain-text email.
 *
 * @param array{0: string, 1?: string}|null $replyTo [email, name]
 * @throws RuntimeException when mail is not configured or sending fails.
 */
function send_mail(string $to, string $subject, string $body, string $fromName = 'Portfolio', ?array $replyTo = null): void
{
    if (!mail_is_configured()) {
        error_log('Mail error: mail.username / mail.password are missing from the private config file.');
        throw new RuntimeException('Email is not configured.');
    }

    try {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = config('mail.host');
        $mailer->Port = config('mail.port');
        $mailer->SMTPAuth = true;
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mailer->SMTPOptions = [
            'ssl' => [
                'peer_name' => config('mail.host'),
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ];
        $mailer->Username = config('mail.username');
        $mailer->Password = config('mail.password');
        $mailer->CharSet = 'UTF-8';
        $mailer->setFrom(config('mail.username'), $fromName);
        $mailer->addAddress($to);

        if ($replyTo) {
            $mailer->addReplyTo($replyTo[0], $replyTo[1] ?? '');
        }

        $mailer->Subject = $subject;
        $mailer->Body = $body;
        $mailer->send();
    } catch (Throwable $exception) {
        error_log('Mail error: ' . $exception->getMessage());
        throw new RuntimeException('The email could not be sent.', 0, $exception);
    }
}
