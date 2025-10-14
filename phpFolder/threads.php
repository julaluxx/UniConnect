<?php
header('Content-Type: application/json');
require 'config.php';
require 'vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

function verifyToken() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Token required']);
        exit;
    }
    $token = str_replace('Bearer ', '', $headers['Authorization']);
    try {
        return JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $decoded = verifyToken(); // Verify token for POST
    $data = json_decode(file_get_contents('php://input'), true);
    $title = $data['title'] ?? '';
    $content = $data['content'] ?? '';
    $category_id = $data['category_id'] ?? 0;
    $author_id = $decoded->user_id; // Use from token

    if (empty($title) || empty($content) || empty($category_id) || empty($author_id)) {
        http_response_code(400);
        echo json_encode(['error' => 'กรุณากรอกข้อมูลให้ครบ']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO threads (title, content, category_id, author_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $content, $category_id, $author_id]);
    echo json_encode(['message' => 'สร้างเธรดสำเร็จ']);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // No auth for GET
    $stmt = $pdo->query("SELECT t.*, c.name as category, u.username as author, 
                         (SELECT COUNT(*) FROM comments WHERE thread_id = t.id) as comments,
                         (SELECT COUNT(*) FROM likes WHERE thread_id = t.id) as likes 
                         FROM threads t 
                         JOIN categories c ON t.category_id = c.id 
                         JOIN users u ON t.author_id = u.id");
    $threads = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($threads);
}
?>