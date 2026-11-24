<?php
require_once __DIR__ . '/config/db.php';
logoutUser();
header('Location: index.php');
exit;
