<?php
/**
 * Shared <head> for every layout.
 * Expects: $pageTitle. Optional: $pageDescription, $noIndex.
 */
$pageDescription ??= 'Alexandra Concepcion is a Virtual Assistant in Cavite, Philippines, offering administrative support, customer service, data entry, and digital support for growing businesses.';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <?php if (!empty($noIndex)): ?>
        <meta name="robots" content="noindex, nofollow">
    <?php else: ?>
        <meta property="og:type" content="website">
        <meta property="og:title" content="<?= e($pageTitle) ?>">
        <meta property="og:description" content="<?= e($pageDescription) ?>">
    <?php endif; ?>
    <meta name="theme-color" content="#fffdf8">
    <meta name="color-scheme" content="light dark">
    <link rel="icon" href="<?= e(asset('assets/images/favicon.svg')) ?>" type="image/svg+xml">
    <script>
        (function (root) {
            root.classList.add('js', 'PageRevealEvent' in window ? 'vt' : 'no-vt');

            // Appearance settings (saved per browser; see assets/js/theme.js and admin/settings.php).
            // Applied here, before the page draws, so it never flashes the wrong theme.
            var read = function (key, allowed, fallback) {
                try {
                    var value = localStorage.getItem(key);
                    return allowed.indexOf(value) > -1 ? value : fallback;
                } catch (error) {
                    return fallback;
                }
            };
            var theme = read('va-theme', ['light', 'dark', 'auto'], 'auto');
            var dark = theme === 'dark' || (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            root.setAttribute('data-theme', dark ? 'dark' : 'light');
            root.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
            root.setAttribute('data-accent', read('va-accent', ['coral', 'teal', 'plum', 'blue'], 'coral'));
            root.setAttribute('data-text', read('va-text', ['sm', 'md', 'lg'], 'md'));

            // Header entrance animation: only on the first page of a visit (see motion.css).
            try {
                var area = /\/admin\/auth\//.test(location.pathname) ? 'auth' : (/\/admin\//.test(location.pathname) ? 'admin' : 'site');
                if (!sessionStorage.getItem('fx-intro-' + area)) {
                    sessionStorage.setItem('fx-intro-' + area, '1');
                    root.classList.add('fx-intro');
                }
            } catch (error) {}
        })(document.documentElement);
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= e(asset('assets/css/style.css')) ?>">
    <?php foreach ($extraStyles ?? [] as $stylesheet): ?>
        <link rel="stylesheet" href="<?= e(asset($stylesheet)) ?>">
    <?php endforeach; ?>
    <link rel="stylesheet" href="<?= e(asset('assets/css/motion.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/theme.css')) ?>">
</head>
