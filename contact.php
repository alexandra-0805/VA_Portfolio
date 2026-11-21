<?php
require __DIR__ . '/app/bootstrap.php';
require APP_PATH . '/lib/mailer.php';

const CONTACT_LIMIT = 5;
const CONTACT_WINDOW = 3600;

$pageTitle = 'Contact | Alexandra Concepcion';
$activePage = 'contact';
$site = config('site');
$errors = [];
$sent = false;
$limits = ['name' => 100, 'email' => 160, 'subject' => 180, 'message' => 5000];
$formData = array_fill_keys(array_keys($limits), '');

if (is_post()) {
    foreach ($limits as $field => $max) {
        $formData[$field] = input($field, $max + 1);
    }

    if (!csrf_is_valid()) {
        $errors[] = 'Your session expired. Please send the form again.';
    } elseif (input('website') !== '') {
        // Hidden honeypot field: only bots fill it. Pretend success.
        $sent = true;
    } elseif (rate_limited('contact', CONTACT_LIMIT, CONTACT_WINDOW)) {
        $errors[] = 'You have sent several messages recently. Please try again later or email me directly.';
    } else {
        if ($formData['name'] === '' || mb_strlen($formData['name']) > $limits['name']) {
            $errors[] = 'Please enter your name (up to 100 characters).';
        }
        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($formData['email']) > $limits['email']) {
            $errors[] = 'Please enter a valid email address.';
        }
        if ($formData['subject'] === '' || mb_strlen($formData['subject']) > $limits['subject']) {
            $errors[] = 'Please enter a subject (up to 180 characters).';
        }
        if ($formData['message'] === '' || mb_strlen($formData['message']) > $limits['message']) {
            $errors[] = 'Please enter a message (up to 5,000 characters).';
        }
    }

    if (!$errors && !$sent) {
        record_attempt('contact', CONTACT_WINDOW);
        db_query(
            "INSERT INTO messages (name, email, subject, message, status) VALUES (?, ?, ?, ?, 'unread')",
            array_values($formData)
        );

        // The message is already saved for the admin inbox; email is a bonus notification.
        try {
            send_mail(
                config('mail.contact_to') ?: $site['email'],
                '[Portfolio] ' . $formData['subject'],
                "Name: {$formData['name']}\nEmail: {$formData['email']}\n\n{$formData['message']}",
                'Portfolio contact form',
                [$formData['email'], $formData['name']]
            );
        } catch (RuntimeException) {
            // Logged inside send_mail(); visitors never see server configuration details.
        }

        $sent = true;
    }

    if ($sent) {
        $formData = array_fill_keys(array_keys($limits), '');
    }
}

require APP_PATH . '/views/site/header.php';
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
                    <a href="mailto:<?= e($site['email']) ?>">
                        <i class="bi bi-envelope" aria-hidden="true"></i>
                        <span><?= e($site['email']) ?></span>
                    </a>
                    <a href="<?= e($site['linkedin']) ?>" target="_blank" rel="noopener">
                        <i class="bi bi-linkedin" aria-hidden="true"></i>
                        <span><?= e(preg_replace('#^https?://(www\.)?#', '', $site['linkedin'])) ?></span>
                    </a>
                    <a href="<?= e($site['github']) ?>" target="_blank" rel="noopener">
                        <i class="bi bi-github" aria-hidden="true"></i>
                        <span><?= e(preg_replace('#^https?://(www\.)?#', '', $site['github'])) ?></span>
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

                    <?= render_alerts($errors, $sent ? "Thank you! Your message has been sent. I'll get back to you within one business day." : '') ?>

                    <form method="post" data-validate novalidate>
                        <?= csrf_field() ?>
                        <div class="visually-hidden" aria-hidden="true">
                            <label for="website">Leave this field empty</label>
                            <input id="website" name="website" tabindex="-1" autocomplete="off">
                        </div>

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

<?php require APP_PATH . '/views/site/footer.php'; ?>
