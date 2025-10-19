<?php
// dashboard_redirect.php
session_start();
require 'pdo.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

switch ($_SESSION['role']) {
    case 'admin':
        header('Location: index.php');
        break;
    case 'moderator':
        header('Location: index.php');
        break;
    case 'user':
    default:
        header('Location: index.php');
        break;
}
exit;
?>