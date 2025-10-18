<?php
// register.php
session_start();
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$confirm = $data['confirm'] ?? '';

if (!$username || !$email || !$password || !$confirm) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบ']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'อีเมลไม่ถูกต้อง']);
    exit;
}
if ($password !== $confirm) {
    echo json_encode(['status' => 'error', 'message' => 'รหัสผ่านไม่ตรงกัน']);
    exit;
}
if (strlen($password) < 6) {
    echo json_encode(['status' => 'error', 'message' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร']);
    exit;
}

try {
    // ตรวจ username / email ซ้ำ
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1");
    $stmt->execute(['username' => $username, 'email' => $email]);
    $exists = $stmt->fetch();
    if ($exists) {
        echo json_encode(['status' => 'error', 'message' => 'Username หรือ Email นี้มีในระบบแล้ว']);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
    $insert->execute(['username' => $username, 'email' => $email, 'password' => $hash]);

    // login อัตโนมัติหลังลงทะเบียน
    $userId = $conn->lastInsertId();
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'user';

    echo json_encode(['status' => 'success', 'message' => 'Registered', 'redirect' => 'index.php']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด']);
}
