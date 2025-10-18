<?php
// pdo.php
// -------------------------------
// ตั้งค่าการเชื่อมต่อฐานข้อมูล
// -------------------------------
$host = 'localhost';
$db   = 'uniconnect_db';
$user = 'uniconnect';
$pass = 'uc_password';
$charset = 'utf8mb4';

// สร้าง DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// ตั้งค่า options สำหรับ PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // แสดงข้อผิดพลาดแบบ Exception
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // คืนค่าแบบ associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                    // ปิด emulate เพื่อใช้ prepared จริง ๆ
];

try {
    // สร้าง object PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // แสดงข้อความ error ถ้าการเชื่อมต่อล้มเหลว
    echo "❌ Database connection failed: " . $e->getMessage();
    exit;
}
?>
