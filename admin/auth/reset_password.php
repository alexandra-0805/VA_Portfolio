<?php
require __DIR__ . '/../../app/bootstrap.php';
redirect_if_admin();

$token = (string) ($_GET['token'] ?? $_POST['token'] ?? '');
$errors = [];
$reset = null;

if (preg_match('/^[a-f0-9]{64}$/', $token)) {
    $reset = db_query(
        'SELECT id, admin_id, otp_hash FROM admin_password_resets '
        . 'WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW() AND otp_attempts < 5 LIMIT 1',
        [hash('sha256', $token)]
    )->fetch() ?: null;
}

if (is_post()) {
    verify_csrf();
    $password = (string) ($_POST['password'] ?? '');

    if (!$reset) {
        $errors[] = 'This reset link is invalid or has expired.';
    } elseif (!one_time_code_matches(input('otp', 6), (string) $reset['otp_hash'])) {
        db_query('UPDATE admin_password_resets SET otp_attempts = otp_attempts + 1 WHERE id = ?', [$reset['id']]);
        $errors[] = 'The reset code is incorrect. After 5 wrong attempts the link stops working.';
    } elseif ($error = password_error($password, (string) ($_POST['password_confirmation'] ?? ''))) {
        $errors[] = $error;
    } else {
        $pdo = db();
        $pdo->beginTransaction();

        try {
            db_query('UPDATE admins SET password = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $reset['admin_id']]);
            db_query('UPDATE admin_password_resets SET used_at = NOW() WHERE admin_id = ? AND used_at IS NULL', [$reset['admin_id']]);
            $pdo->commit();
            redirect('admin/auth/signin.php?reset=1');
        } catch (Throwable $exception) {
            $pdo->rollBack();
            error_log('Password reset failed: ' . $exception->getMessage());
            $errors[] = 'The password could not be updated. Please request a new link.';
        }
    }
} elseif (!$reset) {
    $errors[] = 'This reset link is invalid or has expired.';
}

$pageTitle = 'Set new password';
$authHeading = 'Set a new password';
$authFooter = '<a href="' . e(url('admin/auth/forgot_password.php')) . '">Request a new link</a>'
    . ' <span aria-hidden="true">·</span> <a href="' . e(url('admin/auth/signin.php')) . '">Back to sign in</a>';

require APP_PATH . '/views/auth/header.php';
?>
<?php if ($reset): ?>
    <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <div class="mb-3">
            <label class="form-label" for="otp">Email code</label>
            <input class="form-control" id="otp" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus>
        </div>
        <?php
        foreach ([['password', 'password', 'New password'], ['password-confirmation', 'password_confirmation', 'Confirm password']] as [$fieldId, $fieldName, $fieldLabel]) {
            [$fieldAutocomplete, $fieldMin] = ['new-password', PASSWORD_MIN_LENGTH];
            require APP_PATH . '/views/auth/password_field.php';
        }
        ?>
        <button class="btn btn-primary w-100 mt-2" type="submit">Update password</button>
    </form>
<?php endif; ?>
<?php require APP_PATH . '/views/auth/footer.php'; ?>
