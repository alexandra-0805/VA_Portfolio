<?php
require_once __DIR__ . '/../includes/functions.php';

$destination = is_admin() ? 'dashboard.php' : 'signin.php';
header('Location: ' . $destination);
exit;
