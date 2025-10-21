<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uc-db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // ตั้งค่าให้ใช้ UTF-8 เพื่อรองรับภาษาไทย
    $conn->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage());
}

?>