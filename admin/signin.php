<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_admin()) {
    redirect('dashboard.php');
}

$errorMessage = '';
$successMessage = isset($_GET['reset'])
    ? 'Your password was updated. You can now sign in.'
    : (isset($_GET['signup']) ? 'Your admin account was created. You can now sign in.' : '');
$signinWindowSeconds = 300;
$maxsigninAttempts = 5;
$signinAttempts = $_SESSION['signin_attempts'] ?? [
    'count' => 0,
    'started_at' => time(),
];

if (time() - $signinAttempts['started_at'] >= $signinWindowSeconds) {
    $signinAttempts = ['count' => 0, 'started_at' => time()];
    $_SESSION['signin_attempts'] = $signinAttempts;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(false);

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $usernameIsValid = $username !== '' && strlen($username) <= 80;
    $isRateLimited = $signinAttempts['count'] >= $maxsigninAttempts;

    if ($isRateLimited) {
        $errorMessage = 'Too many attempts. Please wait a few minutes and try again.';
    } elseif ($usernameIsValid) {
        $statement = $pdo->prepare(
            'SELECT * FROM admins WHERE username = ? LIMIT 1'
        );
        $statement->execute([$username]);
        $admin = $statement->fetch();
    } else {
        $admin = null;
    }

    if (!$errorMessage && $admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        unset($_SESSION['signin_attempts']);
        redirect('dashboard.php');
    }

    if (!$errorMessage) {
        $signinAttempts['count']++;
        $_SESSION['signin_attempts'] = $signinAttempts;
        $errorMessage = 'Invalid username or password.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Sign in</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <main class="admin-signin min-vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">
                    <section class="soft-panel admin-auth-card">
                        <div class="admin-auth-brand">
                            <span class="admin-auth-brand-icon" aria-hidden="true">
                                <i class="bi bi-shield-lock"></i>
                            </span>
                            <span>Portfolio admin</span>
                        </div>
                        <div class="section-kicker mb-2">Secure workspace</div>
                        <h1 class="h3 fw-bold mb-2">Welcome back</h1>
                        <p class="muted">
                            Manage your portfolio content and messages.
                        </p>

                        <?php if ($errorMessage): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= e($errorMessage) ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($successMessage): ?>
                            <div class="alert alert-success" role="status">
                                <?= e($successMessage) ?>
                            </div>
                        <?php endif; ?>

                        <form method="post">
                            <input
                                type="hidden"
                                name="csrf"
                                value="<?= e(csrf_token()) ?>"
                            >

                            <div class="mb-3">
                                <label class="form-label" for="username">Username</label>
                                <input
                                    class="form-control"
                                    id="username"
                                    name="username"
                                    autocomplete="username"
                                    required
                                    autofocus
                                >
                            </div>

                            <div class="mb-4 password-field">
                                <label class="form-label" for="password">Password</label>
                                <div class="password-input-wrapper">
                                    <input
                                        class="form-control"
                                        id="password"
                                        type="password"
                                        name="password"
                                        autocomplete="current-password"
                                        required
                                    >
                                    <button class="password-toggle" type="button" data-password-toggle="password" aria-label="Show password" title="Show password">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="text-end mb-4">
                                <a href="forgot_password.php">Forgot password?</a>
                            </div>

                            <button class="btn btn-primary w-100" type="submit">
                                Sign in
                                <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                            </button>
                        </form>
                        <div class="admin-auth-footer">
                            <span>Need an account?</span>
                            <a href="signup.php">
                            <i class="bi bi-person-plus" aria-hidden="true"></i>
                            Sign up
                            </a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
    <script src="auth.js"></script>
</body>
</html>
