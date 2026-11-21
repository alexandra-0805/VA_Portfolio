<?php
require __DIR__ . '/../app/bootstrap.php';

redirect(is_admin() ? 'admin/dashboard.php' : 'admin/auth/signin.php');
