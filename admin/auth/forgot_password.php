<?php
require __DIR__ . '/../../app/bootstrap.php';
require APP_PATH . '/lib/mailer.php';
redirect_if_admin();

const RESET_REQUEST_LIMIT = 5;
const RESET_REQUEST_WINDOW = 3600;

$errors = [];
$success = '';
$genericMessage = 'If that account exists, a reset code and link have been sent to its email address.';

if (is_post()) {
    verify_csrf();
    $identifier = input('identifier', 161);

    if ($identifier === '' || strlen($identifier) > 160) {
        $errors[] = 'Enter your admin username or email address.';
    } elseif (rate_limited('password-reset', RESET_REQUEST_LIMIT, RESET_REQUEST_WINDOW)) {
        $errors[] = 'Too many reset requests. Please try again in an hour.';
    } else {
        record_attempt('password-reset', RESET_REQUEST_WINDOW);
        $admin = db_query(
            'SELECT id, email FROM admins WHERE username = ? OR email = ? LIMIT 1',
            [$identifier, $identifier]
        )->fetch();

        if ($admin && filter_var($admin['email'], FILTER_VALIDATE_EMAIL)) {
            $token = bin2hex(random_bytes(32));
            [$code, $codeHash] = make_one_time_code();
            db_query('DELETE FROM admin_password_resets WHERE admin_id = ?', [$admin['id']]);
            db_query(
                'INSERT INTO admin_password_resets (admin_id, token_hash, otp_hash, expires_at) '
                . 'VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))',
                [$admin['id'], hash('sha256', $token), $codeHash]
            );

            try {
                send_mail(
                    $admin['email'],
                    'Reset your portfolio admin password',
                    "Your password reset code is: {$code}\n\n"
                        . "This code and link expire in 30 minutes.\n\n"
                        . config('app.url') . '/admin/auth/reset_password.php?token=' . $token
                        . "\n\nIf you did not request this, you can ignore this email.",
                    'Portfolio admin'
                );
            } catch (RuntimeException) {
                db_query('DELETE FROM admin_password_resets WHERE admin_id = ?', [$admin['id']]);
                // Only reveal configuration problems in debug mode, never whether the account exists.
                if (config('app.debug')) {
                    $errors[] = 'The reset email could not be sent. Check the mail settings in the private config file.';
                }
            }
        }

        if (!$errors) {
            $success = $genericMessage;
        }
    }
}

$pageTitle = 'Forgot password';
$authHeading = 'Reset password';
$authIntro = 'Enter your admin username or email and we will email you a reset code.';
$authFooter = '<a href="' . e(url('admin/auth/signin.php')) . '">Back to sign in</a>';

require APP_PATH . '/views/auth/header.php';
?>
<form method="post">
    <?= csrf_field() ?>
    <label class="form-label" for="identifier">Username or email</label>
    <input class="form-control mb-4" id="identifier" name="identifier" autocomplete="username" maxlength="160" required autofocus>
    <button class="btn btn-primary w-100" type="submit">Send reset code</button>
</form>
<?php require APP_PATH . '/views/auth/footer.php'; ?>
