<?php
require __DIR__ . '/../../app/bootstrap.php';

// Sign-out is POST-only with a CSRF token, so other sites cannot log you out.
if (is_post() && csrf_is_valid()) {
    logout_admin();
}

redirect(is_admin() ? 'admin/dashboard.php' : 'admin/auth/signin.php');
