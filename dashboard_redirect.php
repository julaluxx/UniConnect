<?php
session_start();

// ถ้ายังไม่ได้ล็อกอิน → กลับไปหน้า login
if(!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

// ตรวจสอบ role แล้ว redirect ไป dashboard ที่เหมาะสม
switch($_SESSION['role']){
    case 'admin':
        header('Location: users/admin_dashboard.php');
        break;
    case 'moderator':
        header('Location: users/moderator_dashboard.php');
        break;
    case 'user':
        header('Location: users/user_dashboard.php');
        break;
    default:
        header('Location: login.php');
}
exit;
?>

