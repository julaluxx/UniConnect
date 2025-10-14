<?php
header('Content-Type: application/json');
require 'config.php';
require 'vendor/autoload.php';
use \Firebase\JWT\JWT;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'กรุณากรอกข้อมูลให้ครบ']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $payload = ['user_id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']];
        $jwt = JWT::encode($payload, JWT_SECRET, 'HS256');
        echo json_encode(['token' => $jwt, 'user' => $payload]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
    }
}
?>