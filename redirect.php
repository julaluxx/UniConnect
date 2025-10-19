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
        header('Location: ./admin/admin_page.php');
        break;
    case 'moderator':
        header('Location: ./modder/modder_page.php');
        break;
    case 'user':
    default:
        header('Location: ./user/user_page.php');
        break;
}
exit;
?>