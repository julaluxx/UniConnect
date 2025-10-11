<?php
header('Content-Type: application/json');
require '../config.php';
require '../vendor/autoload.php';
use \Firebase\JWT\JWT;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $role = $data['role'] ?? 'user';

    if (empty($username) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'กรุณากรอกข้อมูลให้ครบ']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword, $role]);
        $userId = $pdo->lastInsertId();

        $payload = ['user_id' => $userId, 'username' => $username, 'role' => $role];
        $jwt = JWT::encode($payload, 'your_jwt_secret_key', 'HS256');
        echo json_encode(['token' => $jwt, 'user' => $payload]);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
}
?>