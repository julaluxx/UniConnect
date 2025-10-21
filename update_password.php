<?php
require_once 'models/pdo.php';
$hashedPassword = password_hash('dummy-uc', PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->execute([$hashedPassword, 'dummy.uc@email.com']);
echo "รหัสผ่านอัปเดตเรียบร้อย";
?>