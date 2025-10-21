<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ucdb";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8mb4'");
} catch (PDOException $e) {
    error_log("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage());
    die("เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล กรุณาตรวจสอบการกำหนดค่าเซิร์ฟเวอร์");
}
?>