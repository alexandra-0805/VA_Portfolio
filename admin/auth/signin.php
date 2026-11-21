<?php
require __DIR__ . '/../../app/bootstrap.php';
redirect_if_admin();

const SIGNIN_MAX_ATTEMPTS = 5;
const SIGNIN_WINDOW_SECONDS = 900;

$errors = [];
$success = match (true) {
    isset($_GET['reset']) => 'Your password was updated. You can now sign in.',
    isset($_GET['signup']) => 'Your admin account was created. You can now sign in.',
    default => '',
};

if (is_post()) {
    verify_csrf();
    $username = input('username', 80);
    $password = (string) ($_POST['password'] ?? '');

    if (rate_limited('signin', SIGNIN_MAX_ATTEMPTS, SIGNIN_WINDOW_SECONDS)) {
        $errors[] = 'Too many sign-in attempts. Please wait 15 minutes and try again.';
    } else {
        $admin = $username !== ''
            ? db_query('SELECT id, username, password FROM admins WHERE username = ? LIMIT 1', [$username])->fetch()
            : false;

        if ($admin && password_verify($password, $admin['password'])) {
            if (password_needs_rehash($admin['password'], PASSWORD_DEFAULT)) {
                db_query('UPDATE admins SET password = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $admin['id']]);
            }
            clear_attempts('signin');
            login_admin($admin);
            redirect('admin/dashboard.php');
        }

        record_attempt('signin', SIGNIN_WINDOW_SECONDS);
        $errors[] = 'Invalid username or password.';
    }
}

$pageTitle = 'Sign in';
$authHeading = 'Welcome back';
$authIntro = 'Manage your portfolio content and messages.';
$authFooter = config('admin.signup_enabled')
    ? '<span>Need an account?</span> <a href="' . e(url('admin/auth/signup.php')) . '">Sign up</a>'
    : '<a href="' . e(url('index.php')) . '"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back to website</a>';

require APP_PATH . '/views/auth/header.php';
?>
<form method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label" for="username">Username</label>
        <input class="form-control" id="username" name="username" maxlength="80" autocomplete="username" required autofocus>
    </div>

    <?php
    [$fieldId, $fieldName, $fieldLabel, $fieldAutocomplete, $fieldMin] = ['password', 'password', 'Password', 'current-password', 0];
    require APP_PATH . '/views/auth/password_field.php';
    ?>

    <div class="text-end mb-4">
        <a href="<?= e(url('admin/auth/forgot_password.php')) ?>">Forgot password?</a>
    </div>

    <button class="btn btn-primary w-100" type="submit">
        Sign in <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
    </button>
</form>
<?php require APP_PATH . '/views/auth/footer.php'; ?>
