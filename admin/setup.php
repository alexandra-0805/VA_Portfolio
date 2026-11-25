<?php
/**
 * One-time setup for a live host that has no command line (e.g. InfinityFree).
 *
 * Creates the database tables, can load the sample content, and creates the
 * first admin account, so scripts/create_admin.php is not needed on the host.
 *
 * Locked by default: the page only opens while admin.setup_token is set in the
 * private config (app/config.local.php), and it closes for good as soon as an
 * admin account exists. Remove the token after you have signed in.
 */
require __DIR__ . '/../app/bootstrap.php';

const SETUP_MAX_ATTEMPTS = 5;
const SETUP_WINDOW_SECONDS = 900;
const SETUP_TOKEN_MIN_LENGTH = 16;
const SETUP_MIN_PHP = '8.1.0';

$setupToken = (string) config('admin.setup_token', '');

if ($setupToken === '') {
    http_response_code(404);
    exit('Not found.');
}

/**
 * Run a SQL file statement by statement. Skips CREATE DATABASE / USE, because
 * shared hosts give you one database with a fixed name and forbid both.
 */
function setup_run_sql_file(string $file): void
{
    $sql = preg_replace('/^\s*--.*$/m', '', (string) file_get_contents($file));

    foreach (preg_split('/;\s*$/m', (string) $sql) as $statement) {
        $statement = trim($statement);

        if ($statement === '' || preg_match('/^(CREATE\s+DATABASE|USE)\b/i', $statement)) {
            continue;
        }

        db()->exec($statement);
    }
}

// Database connection, checked here (db() would stop the page with a generic error).
$dbError = '';

try {
    new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', config('db.host'), config('db.port'), config('db.name')),
        (string) config('db.user'),
        (string) config('db.password'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
    );
} catch (PDOException $exception) {
    error_log('Setup: database connection failed: ' . $exception->getMessage());
    $dbError = match ((int) ($exception->errorInfo[1] ?? $exception->getCode())) {
        1045 => 'Database sign-in failed: check db.user and db.password.',
        1044, 1049 => 'Database not found: check db.name (on InfinityFree it looks like if0_12345678_portfolio).',
        2002, 2005, 2006 => 'Database server not reachable: check db.host (on InfinityFree it looks like sql123.infinityfree.com).',
        default => 'The database connection failed: check the db settings in the private config.',
    };
}

$schemaFile = ROOT_PATH . '/database/schema.sql';
preg_match_all('/CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\s+`?(\w+)/i', (string) file_get_contents($schemaFile), $matches);
$requiredTables = $matches[1];
$existingTables = [];
$hasContent = false;

if ($dbError === '') {
    $existingTables = db()->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

    // Setup is finished once an admin exists: the page disappears.
    if (in_array('admins', $existingTables, true)
        && (int) db()->query('SELECT COUNT(*) FROM admins')->fetchColumn() > 0) {
        http_response_code(404);
        exit('Not found.');
    }

    $hasContent = in_array('projects', $existingTables, true)
        && (int) db()->query('SELECT COUNT(*) FROM projects')->fetchColumn() > 0;
}

$tablesReady = count(array_intersect($requiredTables, $existingTables));
$tokenTooShort = strlen($setupToken) < SETUP_TOKEN_MIN_LENGTH;
$phpTooOld = version_compare(PHP_VERSION, SETUP_MIN_PHP, '<');
$appUrl = (string) config('app.url');
$urlIsLocal = (bool) preg_match('#^https?://(localhost|127\.0\.0\.1)\b#i', $appUrl);
$canSetUp = $dbError === '' && !$tokenTooShort && !$phpTooOld;

// [state, message]: state is ok, warn, or fail.
$checks = [
    SECRETS_FILE === APP_PATH . '/config.local.php'
        ? ['ok', 'Private settings found: app/config.local.php']
        : (SECRETS_FILE !== ''
            ? ['ok', 'Private settings found outside the web folder']
            : ['fail', 'No private settings file. Upload app/config.local.php (see README).']),
    $tokenTooShort
        ? ['fail', 'admin.setup_token must be at least ' . SETUP_TOKEN_MIN_LENGTH . ' characters.']
        : ['ok', 'Setup token is set'],
    $phpTooOld
        ? ['fail', 'PHP ' . PHP_VERSION . ' is too old. Choose PHP ' . SETUP_MIN_PHP . ' or newer in the host control panel.']
        : ['ok', 'PHP ' . PHP_VERSION],
    match (true) {
        $dbError !== '' => ['fail', $dbError],
        $tablesReady === count($requiredTables) => ['ok', 'Database connected. All ' . count($requiredTables) . ' tables exist.'],
        default => ['warn', 'Database connected. ' . $tablesReady . ' of ' . count($requiredTables) . ' tables exist; setup creates the rest.'],
    },
    $urlIsLocal
        ? ['warn', 'app.url still points to localhost. Set it to your live address so email links work.']
        : ['ok', 'Site address: ' . $appUrl],
];

$errors = [];
$username = '';
$email = '';

if (is_post() && $canSetUp) {
    verify_csrf();
    $username = input('username', 81);
    $email = input('email', 161);
    $password = (string) ($_POST['password'] ?? '');

    if (rate_limited('setup', SETUP_MAX_ATTEMPTS, SETUP_WINDOW_SECONDS)) {
        $errors[] = 'Too many attempts. Please wait 15 minutes and try again.';
    } elseif (!hash_equals($setupToken, (string) ($_POST['setup_token'] ?? ''))) {
        record_attempt('setup', SETUP_WINDOW_SECONDS);
        $errors[] = 'The setup token is wrong.';
    } elseif (!preg_match('/^[A-Za-z0-9._-]{3,80}$/', $username)) {
        $errors[] = 'Use 3–80 letters, numbers, dots, dashes, or underscores for the username.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 160) {
        $errors[] = 'Enter a valid email address.';
    } elseif ($error = password_error($password, (string) ($_POST['password_confirmation'] ?? ''))) {
        $errors[] = $error;
    } else {
        try {
            setup_run_sql_file($schemaFile);

            if (!empty($_POST['sample_content'])) {
                setup_run_sql_file(ROOT_PATH . '/database/seed.sql');
            }

            db_query(
                'INSERT INTO admins (username, email, password) VALUES (?, ?, ?)',
                [$username, $email, password_hash($password, PASSWORD_DEFAULT)]
            );
        } catch (PDOException $exception) {
            error_log('Setup failed: ' . $exception->getMessage());
            $errors[] = 'Database setup failed: ' . $exception->getMessage();
        }

        if (!$errors) {
            clear_attempts('setup');
            flash('success', 'Setup is complete. Sign in, then delete the setup_token line from app/config.local.php.');
            redirect('admin/auth/signin.php');
        }
    }
}

$pageTitle = 'Setup';
$authHeading = 'Set up the admin panel';
$authIntro = 'One-time setup for this host. It creates the database tables and your first admin account.';
$authFooter = '<a href="' . e(url('index.php')) . '"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back to website</a>';
$checkIcons = [
    'ok' => 'bi-check-circle-fill text-success',
    'warn' => 'bi-exclamation-circle-fill text-warning',
    'fail' => 'bi-x-circle-fill text-danger',
];

require APP_PATH . '/views/auth/header.php';
?>
<ul class="list-unstyled small mb-4">
    <?php foreach ($checks as [$state, $message]): ?>
        <li class="d-flex gap-2 mb-2">
            <i class="bi <?= $checkIcons[$state] ?>" aria-hidden="true"></i>
            <span><span class="visually-hidden"><?= e($state) ?>: </span><?= e($message) ?></span>
        </li>
    <?php endforeach; ?>
</ul>

<?php if ($canSetUp): ?>
    <form method="post">
        <?= csrf_field() ?>
        <?php
        [$fieldId, $fieldName, $fieldLabel, $fieldAutocomplete, $fieldMin] = ['setup-token', 'setup_token', 'Setup token (from app/config.local.php)', 'off', 0];
        require APP_PATH . '/views/auth/password_field.php';
        ?>
        <div class="mb-3">
            <label class="form-label" for="username">Admin username</label>
            <input class="form-control" id="username" name="username" value="<?= e($username) ?>" maxlength="80" pattern="[A-Za-z0-9._\-]{3,80}" autocomplete="username" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="email">Admin email</label>
            <input class="form-control" id="email" type="email" name="email" value="<?= e($email) ?>" maxlength="160" autocomplete="email" required>
        </div>
        <?php
        foreach ([['password', 'password', 'Password'], ['password-confirmation', 'password_confirmation', 'Confirm password']] as [$fieldId, $fieldName, $fieldLabel]) {
            [$fieldAutocomplete, $fieldMin] = ['new-password', PASSWORD_MIN_LENGTH];
            require APP_PATH . '/views/auth/password_field.php';
        }
        ?>
        <div class="form-check mb-4">
            <input class="form-check-input" id="sample-content" type="checkbox" name="sample_content" value="1" <?= $hasContent ? '' : 'checked' ?>>
            <label class="form-check-label" for="sample-content">
                Load the portfolio content from database/seed.sql (services, skills, projects, certificates). Existing rows are kept.
            </label>
        </div>
        <button class="btn btn-primary w-100" type="submit">
            <i class="bi bi-rocket-takeoff" aria-hidden="true"></i> Set up and create admin
        </button>
    </form>
<?php else: ?>
    <p class="muted mb-0">Fix the items marked in red in <strong>app/config.local.php</strong>, upload it again, then refresh this page.</p>
<?php endif; ?>
<?php require APP_PATH . '/views/auth/footer.php'; ?>
