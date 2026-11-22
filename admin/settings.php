<?php
require __DIR__ . '/../app/bootstrap.php';
require_admin();

$adminTitle = 'Settings';

// Appearance choices. They are saved in this browser (assets/js/theme.js),
// so they apply to the admin panel and the public site on this device.
$themeChoices = [
    ['light', 'Light', 'Warm paper and ink.', 'is-light'],
    ['dark', 'Dark', 'Easy on the eyes at night.', 'is-dark'],
    ['auto', 'Auto', 'Follows your device setting.', 'is-auto'],
];
$accentChoices = [
    ['coral', 'Coral', '#d86543'],
    ['teal', 'Teal', '#1c6b68'],
    ['plum', 'Plum', '#8a4f9e'],
    ['blue', 'Blue', '#2f6fb5'],
];
$textChoices = [
    ['sm', 'Small', '.9rem'],
    ['md', 'Default', '1.05rem'],
    ['lg', 'Large', '1.25rem'],
];

require APP_PATH . '/views/admin/header.php';
?>
<div class="admin-page-header mb-4">
    <div>
        <div class="section-kicker">Preferences</div>
        <h1 class="h2 fw-bold">Settings</h1>
        <p class="admin-page-intro">
            Customize how the admin panel and your website look on this device. Changes apply straight away.
        </p>
    </div>
</div>

<form class="settings-form" data-settings-form>
    <section class="soft-panel settings-section" aria-labelledby="settings-theme">
        <div class="settings-section-head">
            <h2 class="h5 mb-1" id="settings-theme">Appearance</h2>
            <p class="muted mb-0">Light, dark, or match your device.</p>
        </div>
        <div class="settings-choices settings-choices-3" role="radiogroup" aria-labelledby="settings-theme">
            <?php foreach ($themeChoices as [$value, $label, $hint, $previewClass]): ?>
                <label class="settings-choice">
                    <input class="visually-hidden" type="radio" name="theme" value="<?= e($value) ?>" <?= $value === 'auto' ? 'checked' : '' ?>>
                    <span class="settings-preview <?= e($previewClass) ?>" aria-hidden="true">
                        <span class="settings-preview-bar"></span>
                        <span class="settings-preview-line"></span>
                        <span class="settings-preview-line is-short"></span>
                    </span>
                    <span class="settings-choice-text">
                        <strong><?= e($label) ?></strong>
                        <small><?= e($hint) ?></small>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="soft-panel settings-section" aria-labelledby="settings-accent">
        <div class="settings-section-head">
            <h2 class="h5 mb-1" id="settings-accent">Accent color</h2>
            <p class="muted mb-0">Used for buttons, links, underlines and highlights.</p>
        </div>
        <div class="settings-choices settings-choices-4" role="radiogroup" aria-labelledby="settings-accent">
            <?php foreach ($accentChoices as [$value, $label, $swatch]): ?>
                <label class="settings-choice settings-choice-row">
                    <input class="visually-hidden" type="radio" name="accent" value="<?= e($value) ?>" <?= $value === 'coral' ? 'checked' : '' ?>>
                    <span class="settings-swatch" style="--swatch: <?= e($swatch) ?>" aria-hidden="true"></span>
                    <strong><?= e($label) ?></strong>
                </label>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="soft-panel settings-section" aria-labelledby="settings-text">
        <div class="settings-section-head">
            <h2 class="h5 mb-1" id="settings-text">Text size</h2>
            <p class="muted mb-0">Makes everything easier or more compact to read.</p>
        </div>
        <div class="settings-choices settings-choices-3" role="radiogroup" aria-labelledby="settings-text">
            <?php foreach ($textChoices as [$value, $label, $sample]): ?>
                <label class="settings-choice settings-choice-row">
                    <input class="visually-hidden" type="radio" name="text" value="<?= e($value) ?>" <?= $value === 'md' ? 'checked' : '' ?>>
                    <span class="settings-text-sample" style="font-size: <?= e($sample) ?>" aria-hidden="true">Aa</span>
                    <strong><?= e($label) ?></strong>
                </label>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="settings-footer">
        <p class="muted mb-0">
            Saved in this browser only. Visitors choose light or dark with the sun/moon button on the website.
        </p>
        <div class="d-flex align-items-center gap-3">
            <span class="settings-status" data-settings-status role="status" aria-live="polite"></span>
            <button class="btn btn-outline-dark" type="button" data-settings-reset>Reset to defaults</button>
        </div>
    </div>
</form>
<?php require APP_PATH . '/views/admin/footer.php'; ?>
