<?php
// dashboard_redirect.php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

switch ($_SESSION['role']) {
    case 'admin':
        header('Location: ../admin_page.php');
        break;
    case 'moderator':
        header('Location: ../moder_page.php');
        break;
    case 'user':
    default:
        header('Location: ../user_page.php');
        break;
}
exit;
?>