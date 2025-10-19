<?php
// components/Logout.php
session_start();

// ล้างข้อมูลเซสชันทั้งหมด
session_unset();
session_destroy();

// เปลี่ยนเส้นทางไปยังหน้าแรก
header("Location: index.php");
exit();
?>