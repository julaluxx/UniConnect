<?php
header('Content-Type: application/json');
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // สร้างเธรดใหม่
    $data = json_decode(file_get_contents('php://input'), true);
    $title = $data['title'] ?? '';
    $content = $data['content'] ?? '';
    $category_id = $data['category_id'] ?? 0;
    $author_id = $data['author_id'] ?? 0;

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
    // ดึงเธรดทั้งหมด
    $stmt = $pdo->query("SELECT t.*, c.name as category, u.username as author FROM threads t 
                         JOIN categories c ON t.category_id = c.id 
                         JOIN users u ON t.author_id = u.id");
    $threads = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($threads);
}
?>