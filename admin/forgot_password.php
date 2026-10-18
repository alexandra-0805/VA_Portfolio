<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

if (is_admin()) {
    redirect('dashboard.php');
}

$message = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(false);
    $identifier = trim($_POST['identifier'] ?? '');

    if ($identifier === '' || strlen($identifier) > 160) {
        $errorMessage = 'Enter your admin username or email address.';
    } else {
        $statement = $pdo->prepare(
            'SELECT id, username, email FROM admins '
            . 'WHERE username = :username OR email = :email LIMIT 1'
        );
        $statement->execute([
            'username' => $identifier,
            'email' => $identifier,
        ]);
        $admin = $statement->fetch();

        if ($admin && filter_var($admin['email'], FILTER_VALIDATE_EMAIL)) {
            $token = bin2hex(random_bytes(32));
            $otp = (string) random_int(100000, 999999);
            $tokenHash = hash('sha256', $token);
            $otpHash = hash('sha256', $otp);
            $pdo->prepare('DELETE FROM admin_password_resets WHERE admin_id = ?')
                ->execute([$admin['id']]);
            $pdo->prepare(
                'INSERT INTO admin_password_resets '
                . '(admin_id, token_hash, otp_hash, expires_at) '
                . 'VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))'
            )->execute([$admin['id'], $tokenHash, $otpHash]);

            $appUrl = rtrim(
                getenv('PORTFOLIO_APP_URL') ?: 'http://localhost/VA_AboutMe',
                '/'
            );
            $resetUrl = $appUrl . '/admin/reset_password.php?token=' . $token;
            $smtpUser = getenv('PORTFOLIO_SMTP_USER') ?: 'lykemaeconcepcion@gmail.com';
            $smtpPassword = trim((string) getenv('PORTFOLIO_SMTP_APP_PASSWORD'));

            if (!$smtpPassword) {
                $pdo->prepare('DELETE FROM admin_password_resets WHERE admin_id = ?')
                    ->execute([$admin['id']]);
                $errorMessage = 'Password reset email is not configured. Set PORTFOLIO_SMTP_APP_PASSWORD in Apache, then restart Apache.';
            } else {
                try {
                    $mailer = new PHPMailer(true);
                    $mailer->isSMTP();
                    $mailer->Host = 'smtp.gmail.com';
                    $mailer->SMTPOptions = [
                        'ssl' => [
                            'peer_name' => 'smtp.gmail.com',
                            'verify_peer' => true,
                            'verify_peer_name' => true,
                        ],
                    ];
                    $mailer->SMTPAuth = true;
                    $mailer->Username = $smtpUser;
                    $mailer->Password = $smtpPassword;
                    $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mailer->Port = 587;
                    $mailer->CharSet = 'UTF-8';
                    $mailer->setFrom($smtpUser, 'Portfolio admin');
                    $mailer->addAddress($admin['email']);
                    $mailer->Subject = 'Reset your portfolio admin password';
                    $mailer->Body = "Your password reset code is: {$otp}\n\n"
                        . "This code and link expire in 30 minutes.\n\n"
                        . $resetUrl;
                    $mailer->send();
                    $message = 'A reset code and link have been sent to the email address on your admin account.';
                } catch (Exception $exception) {
                    $pdo->prepare('DELETE FROM admin_password_resets WHERE admin_id = ?')
                        ->execute([$admin['id']]);
                    $errorMessage = 'The reset email could not be sent. Check the Gmail App Password and Apache SMTP settings.';
                    error_log('Password reset mail error: ' . $exception->getMessage());
                }
            }

            if (!$message && !$errorMessage) {
                $message = 'If that account exists, a reset code and link have been sent.';
            }
        } else {
            $message = 'If that account exists, a reset code and link have been sent.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <main class="admin-login min-vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">
                    <section class="soft-panel">
                        <div class="brand-mark mb-4">Admin</div>
                        <h1 class="h3 fw-bold">Reset password</h1>
                        <p class="muted">Enter your admin username or email and we will email a reset code.</p>

                        <?php if ($message): ?>
                            <div class="alert alert-success" role="status"><?= e($message) ?></div>
                        <?php endif; ?>
                        <?php if ($errorMessage): ?>
                            <div class="alert alert-danger" role="alert"><?= e($errorMessage) ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <label class="form-label" for="identifier">Username or email</label>
                            <input class="form-control mb-4" id="identifier" name="identifier" autocomplete="username" maxlength="160" required autofocus>
                            <button class="btn btn-primary w-100" type="submit">Send reset code</button>
                        </form>
                        <a class="d-block mt-3 text-center" href="signin.php">Back to sign in</a>
                    </section>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
