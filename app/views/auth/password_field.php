<?php
/**
 * Password input with a show/hide toggle.
 * Expects: $fieldId, $fieldName, $fieldLabel, $fieldAutocomplete. Optional: $fieldMin.
 */
?>
<div class="mb-3 password-field">
    <label class="form-label" for="<?= e($fieldId) ?>"><?= e($fieldLabel) ?></label>
    <div class="password-input-wrapper">
        <input class="form-control" id="<?= e($fieldId) ?>" type="password" name="<?= e($fieldName) ?>"
            autocomplete="<?= e($fieldAutocomplete) ?>" maxlength="1024"
            <?= !empty($fieldMin) ? 'minlength="' . (int) $fieldMin . '"' : '' ?> required>
        <button class="password-toggle" type="button" data-password-toggle="<?= e($fieldId) ?>" aria-label="Show password" title="Show password">
            <i class="bi bi-eye" aria-hidden="true"></i>
        </button>
    </div>
</div>
