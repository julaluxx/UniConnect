<?php
// db.php
$host = 'localhost';
$db = 'uniconnect_db';
$user = 'uniconnect';
$pass = 'uc_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // ปล่อยข้อมูล error แบบจำกัด เพื่อไม่ให้แสดงข้อมูลความลับ
    echo "Database connection failed.";
    exit;
}
?>
