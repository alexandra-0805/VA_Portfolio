    </main>

    <footer class="site-footer">
        <div class="container py-5">
            <div class="row g-4 align-items-end">
                <div class="col-lg-6">
                    <div class="brand-mark mb-3">Alexandra<span>.</span></div>
                    <p class="mb-0 text-muted">
                        Reliable support for thoughtful businesses and ambitious
                        digital projects.
                    </p>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <div class="social-links mb-3">
                        <a
                            href="mailto:lykemaeconcepcion@gmail.com"
                            aria-label="Email"
                        >
                            <i class="bi bi-envelope" aria-hidden="true"></i>
                        </a>
                        <a
                            href="https://www.linkedin.com/in/alexandra-concepcion"
                            target="_blank"
                            rel="noopener"
                            aria-label="LinkedIn"
                        >
                            <i class="bi bi-linkedin" aria-hidden="true"></i>
                        </a>
                        <a
                            href="https://github.com/alexandra-0805"
                            target="_blank"
                            rel="noopener"
                            aria-label="GitHub"
                        >
                            <i class="bi bi-github" aria-hidden="true"></i>
                        </a>
                    </div>
                    <small class="text-muted">
                        &copy; <?= date('Y') ?> Alexandra Concepcion
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <?php require __DIR__ . '/chatbot.php'; ?>

    <button id="backToTop" class="back-top" aria-label="Back to top">
        <i class="bi bi-arrow-up" aria-hidden="true"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js?v=3"></script>
</body>
</html>
