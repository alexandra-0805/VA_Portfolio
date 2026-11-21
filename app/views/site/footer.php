<?php
/**
 * Public site layout (bottom).
 */
$site = config('site');
?>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a class="brand-mark" href="<?= e(url('index.php')) ?>">Alexandra<span>.</span></a>
                    <p>Reliable virtual support for thoughtful businesses and ambitious digital projects.</p>
                    <div class="social-links">
                        <a href="mailto:<?= e($site['email']) ?>" aria-label="Email Alexandra"><i class="bi bi-envelope" aria-hidden="true"></i></a>
                        <a href="<?= e($site['linkedin']) ?>" target="_blank" rel="noopener" aria-label="LinkedIn (opens in a new tab)"><i class="bi bi-linkedin" aria-hidden="true"></i></a>
                        <a href="<?= e($site['github']) ?>" target="_blank" rel="noopener" aria-label="GitHub (opens in a new tab)"><i class="bi bi-github" aria-hidden="true"></i></a>
                    </div>
                </div>

                <nav class="footer-col" aria-label="Footer navigation">
                    <h2 class="footer-heading">Explore</h2>
                    <ul>
                        <li><a href="<?= e(url('about.php')) ?>">About</a></li>
                        <li><a href="<?= e(url('services.php')) ?>">Services</a></li>
                        <li><a href="<?= e(url('projects.php')) ?>">Work</a></li>
                        <li><a href="<?= e(url('resume.php')) ?>">Resume</a></li>
                    </ul>
                </nav>

                <div class="footer-col">
                    <h2 class="footer-heading">Get in touch</h2>
                    <ul>
                        <li><a href="mailto:<?= e($site['email']) ?>"><i class="bi bi-envelope" aria-hidden="true"></i><?= e($site['email']) ?></a></li>
                        <li><i class="bi bi-geo-alt" aria-hidden="true"></i><?= e($site['location']) ?> (GMT+8)</li>
                        <li><i class="bi bi-clock" aria-hidden="true"></i>Replies within one business day</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <small>&copy; <?= date('Y') ?> <?= e($site['name']) ?>. All rights reserved.</small>
                <a href="<?= e(url('contact.php')) ?>" class="text-link">
                    Start a project <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </footer>

    <?php $chatbotContext = 'site'; require APP_PATH . '/views/partials/chatbot.php'; ?>

    <button id="backToTop" class="back-top" type="button" aria-label="Back to top">
        <i class="bi bi-arrow-up" aria-hidden="true"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= e(asset('assets/js/main.js')) ?>"></script>
    <script src="<?= e(asset('assets/js/chatbot.js')) ?>"></script>
</body>
</html>
