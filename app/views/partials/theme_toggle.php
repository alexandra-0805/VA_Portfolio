<?php
/**
 * Sun / moon button that switches light and dark mode (assets/js/theme.js).
 * Optional: $themeToggleClass for extra classes.
 */
?>
<button class="theme-toggle <?= e($themeToggleClass ?? '') ?>" type="button" data-theme-toggle aria-label="Switch to dark mode" title="Switch to dark mode" aria-pressed="false">
    <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
    </svg>
    <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
        <circle cx="12" cy="12" r="4.5"/>
        <path d="M12 1.5v2M12 20.5v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1.5 12h2M20.5 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
    </svg>
</button>
