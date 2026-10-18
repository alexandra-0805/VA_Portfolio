<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$pageTitle = 'Contact | Alexandra Concepcion';
$activePage = 'contact';
$errors = [];
$formData = [
    'name' => '',
    'email' => '',
    'subject' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'subject' => trim($_POST['subject'] ?? ''),
        'message' => trim($_POST['message'] ?? ''),
    ];

    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        $errors[] = 'Security token expired. Please try again.';
    }

    if ($formData['name'] === '' || strlen($formData['name']) > 100) {
        $errors[] = 'Please enter your name.';
    }

    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email.';
    }

    if ($formData['subject'] === '' || strlen($formData['subject']) > 180) {
        $errors[] = 'Please enter a subject.';
    }

    if ($formData['message'] === '' || strlen($formData['message']) > 5000) {
        $errors[] = 'Please enter a message.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO messages (name, email, subject, message, status) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $formData['name'],
            $formData['email'],
            $formData['subject'],
            $formData['message'],
            'unread',
        ]);

        $recipient = getenv('PORTFOLIO_CONTACT_EMAIL')
            ?: 'lykemaeconcepcion@gmail.com';
        $smtpUser = getenv('PORTFOLIO_SMTP_USER')
            ?: 'lykemaeconcepcion@gmail.com';
        $smtpPassword = getenv('PORTFOLIO_SMTP_APP_PASSWORD');
        $emailSent = false;
        $emailError = null;

        if (!$smtpPassword) {
            $emailError = 'Gmail App Password is not configured for Apache.';
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
                $mailer->setFrom($smtpUser, 'Portfolio contact form');
                $mailer->addAddress($recipient);
                $mailer->addReplyTo($formData['email'], $formData['name']);
                $mailer->Subject = '[Portfolio] ' . $formData['subject'];
                $mailer->Body = "Name: {$formData['name']}\n"
                    . "Email: {$formData['email']}\n\n"
                    . $formData['message'];
                $emailSent = $mailer->send();
            } catch (Exception $exception) {
                $emailSent = false;
                $emailError = 'Gmail rejected the SMTP connection. Check the App Password.';
                error_log('Portfolio mail error: ' . $exception->getMessage());
            }
        }

        $sent = $emailSent;
        $formData = [
            'name' => '',
            'email' => '',
            'subject' => '',
            'message' => '',
        ];
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <div class="section-kicker mb-3">Contact</div>
        <h1 class="section-title">Let's start a <span>conversation.</span></h1>
        <p class="hero-copy mt-4">
            Tell me a little about what you need help with.
        </p>
    </div>
</section>

<section>
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5 contact-details">
                <div class="section-kicker mb-3">Reach out</div>
                <h2 class="h3 fw-bold mb-4">Find me here</h2>
                <p class="muted">
                    For project inquiries, collaboration, or just to say hello,
                    send a note and I will get back to you.
                </p>
                <p class="contact-response-time">
                    <i class="bi bi-clock" aria-hidden="true"></i>
                    I usually respond within one business day.
                </p>

                <div class="contact-links mt-4">
                    <a href="mailto:lykemaeconcepcion@gmail.com">
                        <i class="bi bi-envelope" aria-hidden="true"></i>
                        <span>lykemaeconcepcion@gmail.com</span>
                    </a>
                    <a
                        href="https://www.linkedin.com/in/alexandra-concepcion"
                        target="_blank"
                        rel="noopener"
                    >
                        <i class="bi bi-linkedin" aria-hidden="true"></i>
                        <span>linkedin.com/in/alexandra-concepcion</span>
                    </a>
                    <a
                        href="https://github.com/alexandra-0805"
                        target="_blank"
                        rel="noopener"
                    >
                        <i class="bi bi-github" aria-hidden="true"></i>
                        <span>github.com/alexandra-0805</span>
                    </a>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="soft-panel contact-form-card">
                    <div class="section-kicker mb-3">Send a note</div>
                    <h2 class="h3 fw-bold mb-4">Tell me about your project</h2>
                    <p class="form-intro">
                        Share a few details and I will follow up with the next steps.
                    </p>

                    <?php if (!empty($sent)): ?>
                        <div class="alert alert-success" role="status">
                            Thank you! Your message has been sent successfully.
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($emailError)): ?>
                        <div class="alert alert-warning" role="status">
                            Your message was saved, but <?= e($emailError) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($errors): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php foreach ($errors as $error): ?>
                                <div><?= e($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" data-validate novalidate>
                        <input
                            type="hidden"
                            name="csrf"
                            value="<?= e(csrf_token()) ?>"
                        >

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="name">Full name</label>
                                <input
                                    class="form-control"
                                    id="name"
                                    name="name"
                                    value="<?= e($formData['name']) ?>"
                                    autocomplete="name"
                                    required
                                    maxlength="100"
                                >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="email">Email</label>
                                <input
                                    class="form-control"
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="<?= e($formData['email']) ?>"
                                    autocomplete="email"
                                    required
                                    maxlength="160"
                                >
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="subject">Subject</label>
                                <input
                                    class="form-control"
                                    id="subject"
                                    name="subject"
                                    value="<?= e($formData['subject']) ?>"
                                    required
                                    maxlength="180"
                                >
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="message">Message</label>
                                <textarea
                                    class="form-control"
                                    id="message"
                                    name="message"
                                    rows="6"
                                    required
                                    maxlength="5000"
                                ><?= e($formData['message']) ?></textarea>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary" type="submit">
                                    Send message
                                    <i class="bi bi-send" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
