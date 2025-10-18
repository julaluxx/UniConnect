<?php
session_start();
require 'pdo.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ต้องเข้าสู่ระบบก่อน']);
    exit;
}

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0);

if (!$title || !$content || !$category_id) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบ']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO threads (title, content, category_id, user_id, created_at) VALUES (:title, :content, :category, :user, NOW())");
    $stmt->execute([
        'title' => $title,
        'content' => $content,
        'category' => $category_id,
        'user' => $_SESSION['user_id']
    ]);

    echo json_encode(['status' => 'success', 'message' => 'สร้างกระทู้เรียบร้อย']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: '.$e->getMessage()]);
}
?>
