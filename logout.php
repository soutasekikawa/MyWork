<?php
require_once __DIR__ . '/include/db_connect.php';
require_once __DIR__ . '/include/utils.php';
$user = $_SESSION['username'] ?? 'guest';
session_unset();
session_destroy();
write_log($user, 'ログアウト');
header('Location: index.php');
exit;
