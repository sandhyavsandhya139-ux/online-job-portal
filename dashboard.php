<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
authRequired();
$user = currentUser();

if ($user['role'] === 'admin') {
    header('Location: admin_dashboard.php');
    exit;
}

if ($user['role'] === 'employer') {
    header('Location: employer_dashboard.php');
    exit;
}

header('Location: seeker_dashboard.php');
exit;
