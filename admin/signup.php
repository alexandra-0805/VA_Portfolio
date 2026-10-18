<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

if (is_admin()) {
    redirect('dashboard.php');
}

$errorMessage = '';
$successMessage = '';
$pendingSignup = $_SESSION['pending_signup'] ?? null;
$verificationStep = is_array($pendingSignup)
    && ($pendingSignup['expires_at'] ?? 0) > time();

if (!$verificationStep) {
    unset($_SESSION['pending_signup']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(false);

    if (isset($_POST['verification_code'])) {
        $verificationCode = trim($_POST['verification_code']);
        $pendingSignup = $_SESSION['pending_signup'] ?? null;

        if (!$pendingSignup || ($pendingSignup['expires_at'] ?? 0) <= time()) {
            $errorMessage = 'This verification code has expired. Request a new code.';
            unset($_SESSION['pending_signup']);
        } elseif (!preg_match('/^\d{6}$/', $verificationCode)
            || !hash_equals($pendingSignup['code_hash'], hash('sha256', $verificationCode))) {
            $errorMessage = 'The verification code is invalid.';
        } else {
            $statement = $pdo->prepare(
                'SELECT COUNT(*) FROM admins WHERE username = ? OR email = ?'
            );
            $statement->execute([$pendingSignup['username'], $pendingSignup['email']]);

            if ((int) $statement->fetchColumn() > 0) {
                $errorMessage = 'That username or email is already registered.';
                unset($_SESSION['pending_signup']);
            } else {
                $statement = $pdo->prepare(
                    'INSERT INTO admins (username, email, password) VALUES (?, ?, ?)'
                );
                $statement->execute([
                    $pendingSignup['username'],
                    $pendingSignup['email'],
                    $pendingSignup['password_hash'],
                ]);
                unset($_SESSION['pending_signup']);
                redirect('signin.php?signup=1');
            }
        }
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmation = $_POST['password_confirmation'] ?? '';

        if ($username === '' || strlen($username) > 80) {
            $errorMessage = 'Enter a username up to 80 characters.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 160) {
            $errorMessage = 'Enter a valid email address.';
        } elseif (strlen($password) < 12) {
            $errorMessage = 'Your password must be at least 12 characters.';
        } elseif ($password !== $confirmation) {
            $errorMessage = 'The password confirmation does not match.';
        } else {
            $statement = $pdo->prepare(
                'SELECT COUNT(*) FROM admins WHERE username = ? OR email = ?'
            );
            $statement->execute([$username, $email]);

            if ((int) $statement->fetchColumn() > 0) {
                $errorMessage = 'That username or email is already registered.';
            } else {
                $verificationCode = (string) random_int(100000, 999999);
                $smtpUser = getenv('PORTFOLIO_SMTP_USER') ?: 'lykemaeconcepcion@gmail.com';
                $smtpPassword = trim((string) getenv('PORTFOLIO_SMTP_APP_PASSWORD'));

                if (!$smtpUser || !$smtpPassword) {
                    $errorMessage = 'Email verification is not configured yet.';
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
                        $mailer->addAddress($email);
                        $mailer->Subject = 'Your portfolio admin signup code';
                        $mailer->Body = "Your verification code is: {$verificationCode}\n\n"
                            . 'This code expires in 15 minutes.';
                        $mailer->send();

                        $_SESSION['pending_signup'] = [
                            'username' => $username,
                            'email' => $email,
                            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                            'code_hash' => hash('sha256', $verificationCode),
                            'expires_at' => time() + 900,
                        ];
                        $verificationStep = true;
                        $successMessage = 'A verification code was sent to your email.';
                    } catch (Exception $exception) {
                        error_log('Admin signup mail error: ' . $exception->getMessage());
                        $errorMessage = 'The verification email could not be sent. Check Gmail SMTP settings.';
                    }
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <main class="admin-login min-vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <section class="soft-panel admin-auth-card">
                        <div class="admin-auth-brand">
                            <span class="admin-auth-brand-icon" aria-hidden="true">
                                <i class="bi bi-shield-lock"></i>
                            </span>
                            <span>Portfolio admin</span>
                        </div>
                        <div class="section-kicker mb-2">Secure workspace</div>
                        <h1 class="h3 fw-bold mb-2">Create admin account</h1>
                        <p class="muted">
                            <?= $verificationStep ? 'Enter the code sent to your email.' : 'We will send a verification code to your email.' ?>
                        </p>

                        <?php if ($errorMessage): ?>
                            <div class="alert alert-danger" role="alert"><?= e($errorMessage) ?></div>
                        <?php endif; ?>
                        <?php if ($successMessage): ?>
                            <div class="alert alert-success" role="status"><?= e($successMessage) ?></div>
                        <?php endif; ?>

                        <?php if ($verificationStep): ?>
                            <form method="post">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <div class="mb-4">
                                    <label class="form-label" for="verification-code">Email verification code</label>
                                    <input class="form-control" id="verification-code" type="text" name="verification_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus>
                                </div>
                                <button class="btn btn-primary w-100" type="submit">
                                    <i class="bi bi-shield-check" aria-hidden="true"></i>
                                    Verify and sign up
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="post">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <div class="mb-3">
                                    <label class="form-label" for="username">Username</label>
                                    <input class="form-control" id="username" name="username" maxlength="80" autocomplete="username" required autofocus>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input class="form-control" id="email" type="email" name="email" maxlength="160" autocomplete="email" required>
                                </div>
                                <div class="mb-3 password-field">
                                    <label class="form-label" for="password">Password</label>
                                    <div class="password-input-wrapper">
                                        <input class="form-control" id="password" type="password" name="password" minlength="12" autocomplete="new-password" required>
                                        <button class="password-toggle" type="button" data-password-toggle="password" aria-label="Show password" title="Show password">
                                            <i class="bi bi-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-4 password-field">
                                    <label class="form-label" for="password-confirmation">Confirm password</label>
                                    <div class="password-input-wrapper">
                                        <input class="form-control" id="password-confirmation" type="password" name="password_confirmation" minlength="12" autocomplete="new-password" required>
                                        <button class="password-toggle" type="button" data-password-toggle="password-confirmation" aria-label="Show password" title="Show password">
                                            <i class="bi bi-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <button class="btn btn-primary w-100" type="submit">
                                    <i class="bi bi-envelope-check" aria-hidden="true"></i>
                                    Get verification code
                                </button>
                            </form>
                        <?php endif; ?>
                        <div class="admin-auth-footer">
                            <span>Already have an account?</span>
                            <a href="signin.php">Back to sign in</a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
    <script src="auth.js"></script>
</body>
</html>
