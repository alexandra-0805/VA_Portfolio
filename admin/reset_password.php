<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_admin()) {
    redirect('dashboard.php');
}

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$tokenIsValid = (bool) preg_match('/^[a-f0-9]{64}$/', $token);
$reset = null;
$errorMessage = '';

if ($tokenIsValid) {
    $statement = $pdo->prepare(
        'SELECT resets.id, resets.admin_id, resets.otp_hash, resets.otp_attempts '
        . 'FROM admin_password_resets AS resets '
        . 'INNER JOIN admins ON admins.id = resets.admin_id '
        . 'WHERE resets.token_hash = ? AND resets.used_at IS NULL AND resets.expires_at > NOW() '
        . 'AND resets.otp_attempts < 5 LIMIT 1'
    );
    $statement->execute([hash('sha256', $token)]);
    $reset = $statement->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(false);
    $otp = trim($_POST['otp'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmation = $_POST['password_confirmation'] ?? '';

    if (!$reset) {
        $errorMessage = 'This reset link is invalid or has expired.';
    } elseif (!preg_match('/^\d{6}$/', $otp)
        || !hash_equals((string) $reset['otp_hash'], hash('sha256', $otp))) {
        $pdo->prepare(
            'UPDATE admin_password_resets SET otp_attempts = otp_attempts + 1 WHERE id = ?'
        )->execute([$reset['id']]);
        $reset = null;
        $errorMessage = 'The reset code is invalid or has expired.';
    } elseif (strlen($password) < 12) {
        $errorMessage = 'Your new password must be at least 12 characters.';
    } elseif ($password !== $confirmation) {
        $errorMessage = 'The password confirmation does not match.';
    } else {
        $pdo->beginTransaction();
        try {
            $pdo->prepare('UPDATE admins SET password = ? WHERE id = ?')
                ->execute([password_hash($password, PASSWORD_DEFAULT), $reset['admin_id']]);
            $pdo->prepare('UPDATE admin_password_resets SET used_at = NOW() WHERE id = ?')
                ->execute([$reset['id']]);
            $pdo->commit();
            redirect('signin.php?reset=1');
        } catch (Throwable $exception) {
            $pdo->rollBack();
            $errorMessage = 'The password could not be updated. Please request a new link.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set New Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <main class="admin-login min-vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">
                    <section class="soft-panel">
                        <div class="brand-mark mb-4">Admin</div>
                        <h1 class="h3 fw-bold">Set a new password</h1>

                        <?php if ($errorMessage): ?>
                            <div class="alert alert-danger" role="alert"><?= e($errorMessage) ?></div>
                        <?php endif; ?>

                        <?php if ($reset): ?>
                            <form method="post">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="token" value="<?= e($token) ?>">
                                <div class="mb-3">
                                    <label class="form-label" for="otp">Email code</label>
                                    <input class="form-control" id="otp" type="text" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus>
                                </div>
                                <div class="mb-3 password-field">
                                    <label class="form-label" for="password">New password</label>
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
                                <button class="btn btn-primary w-100" type="submit">Update password</button>
                            </form>
                        <?php elseif (!$errorMessage): ?>
                            <div class="alert alert-danger" role="alert">This reset link is invalid or has expired.</div>
                        <?php endif; ?>
                        <a class="d-block mt-3 text-center" href="signin.php">Back to sign in</a>
                    </section>
                </div>
            </div>
        </div>
    </main>
    <script src="auth.js"></script>
</body>
</html>
