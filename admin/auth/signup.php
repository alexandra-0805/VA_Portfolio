<?php
/**
 * Admin registration with email verification.
 * Closed by default: set admin.signup_enabled => true in the private config to open it temporarily.
 */
require __DIR__ . '/../../app/bootstrap.php';
require APP_PATH . '/lib/mailer.php';
redirect_if_admin();

const SIGNUP_CODE_TTL = 900;
const SIGNUP_MAX_ATTEMPTS = 5;

$pageTitle = 'Sign up';
$authHeading = 'Create admin account';
$authFooter = '<span>Already have an account?</span> <a href="' . e(url('admin/auth/signin.php')) . '">Back to sign in</a>';
$errors = [];
$success = '';

if (!config('admin.signup_enabled')) {
    http_response_code(403);
    $authIntro = 'Admin registration is closed. Ask the site owner for access.';
    require APP_PATH . '/views/auth/header.php';
    require APP_PATH . '/views/auth/footer.php';
    exit;
}

$pending = $_SESSION['pending_signup'] ?? null;

if (!is_array($pending) || ($pending['expires_at'] ?? 0) <= time()) {
    $pending = null;
    unset($_SESSION['pending_signup']);
}

$accountExists = static fn (string $username, string $email): bool => (int) db_query(
    'SELECT COUNT(*) FROM admins WHERE username = ? OR email = ?',
    [$username, $email]
)->fetchColumn() > 0;

if (is_post()) {
    verify_csrf();

    if (rate_limited('signup', SIGNUP_MAX_ATTEMPTS, SIGNUP_CODE_TTL)) {
        $errors[] = 'Too many attempts. Please wait 15 minutes and try again.';
    } elseif (isset($_POST['verification_code'])) {
        record_attempt('signup', SIGNUP_CODE_TTL);

        if (!$pending) {
            $errors[] = 'This verification code has expired. Request a new code.';
        } elseif (!one_time_code_matches(input('verification_code', 6), $pending['code_hash'])) {
            $errors[] = 'The verification code is invalid.';
        } elseif ($accountExists($pending['username'], $pending['email'])) {
            $errors[] = 'That username or email is already registered.';
            unset($_SESSION['pending_signup']);
            $pending = null;
        } else {
            db_query(
                'INSERT INTO admins (username, email, password) VALUES (?, ?, ?)',
                [$pending['username'], $pending['email'], $pending['password_hash']]
            );
            unset($_SESSION['pending_signup']);
            clear_attempts('signup');
            redirect('admin/auth/signin.php?signup=1');
        }
    } else {
        $username = input('username', 81);
        $email = input('email', 161);
        $password = (string) ($_POST['password'] ?? '');

        if (!preg_match('/^[A-Za-z0-9._-]{3,80}$/', $username)) {
            $errors[] = 'Use 3–80 letters, numbers, dots, dashes, or underscores for the username.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 160) {
            $errors[] = 'Enter a valid email address.';
        } elseif ($error = password_error($password, (string) ($_POST['password_confirmation'] ?? ''))) {
            $errors[] = $error;
        } elseif ($accountExists($username, $email)) {
            $errors[] = 'That username or email is already registered.';
        } else {
            record_attempt('signup', SIGNUP_CODE_TTL);
            [$code, $codeHash] = make_one_time_code();

            try {
                send_mail(
                    $email,
                    'Your portfolio admin signup code',
                    "Your verification code is: {$code}\n\nThis code expires in 15 minutes.",
                    'Portfolio admin'
                );
                $_SESSION['pending_signup'] = [
                    'username' => $username,
                    'email' => $email,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'code_hash' => $codeHash,
                    'expires_at' => time() + SIGNUP_CODE_TTL,
                ];
                $pending = $_SESSION['pending_signup'];
                $success = 'A verification code was sent to your email.';
            } catch (RuntimeException) {
                $errors[] = mail_is_configured()
                    ? 'The verification email could not be sent. Please try again later.'
                    : 'Email verification is not configured yet.';
            }
        }
    }
}

$authIntro = $pending ? 'Enter the code sent to your email.' : 'We will send a verification code to your email.';
require APP_PATH . '/views/auth/header.php';
?>
<?php if ($pending): ?>
    <form method="post">
        <?= csrf_field() ?>
        <div class="mb-4">
            <label class="form-label" for="verification-code">Email verification code</label>
            <input class="form-control" id="verification-code" name="verification_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus>
        </div>
        <button class="btn btn-primary w-100" type="submit">
            <i class="bi bi-shield-check" aria-hidden="true"></i> Verify and sign up
        </button>
    </form>
<?php else: ?>
    <form method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label" for="username">Username</label>
            <input class="form-control" id="username" name="username" maxlength="80" pattern="[A-Za-z0-9._\-]{3,80}" autocomplete="username" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input class="form-control" id="email" type="email" name="email" maxlength="160" autocomplete="email" required>
        </div>
        <?php
        foreach ([['password', 'password', 'Password'], ['password-confirmation', 'password_confirmation', 'Confirm password']] as [$fieldId, $fieldName, $fieldLabel]) {
            [$fieldAutocomplete, $fieldMin] = ['new-password', PASSWORD_MIN_LENGTH];
            require APP_PATH . '/views/auth/password_field.php';
        }
        ?>
        <button class="btn btn-primary w-100 mt-2" type="submit">
            <i class="bi bi-envelope-check" aria-hidden="true"></i> Get verification code
        </button>
    </form>
<?php endif; ?>
<?php require APP_PATH . '/views/auth/footer.php'; ?>
