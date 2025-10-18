<?php
// create_thread.php
session_start();
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'Invalid request']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error','message'=>'ต้องล็อกอินก่อน']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$title = trim($data['title'] ?? '');
$content = trim($data['content'] ?? '');
$category_id = intval($data['category_id'] ?? 0);

if (!$title || !$content || !$category_id) {
    echo json_encode(['status'=>'error','message'=>'กรุณากรอกข้อมูลให้ครบ']);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO threads (title, content, category_id, author_id) VALUES (:title, :content, :category_id, :author_id)");
    $stmt->execute([
        'title' => $title,
        'content' => $content,
        'category_id' => $category_id,
        'author_id' => $_SESSION['user_id']
    ]);
    echo json_encode(['status'=>'success','message'=>'สร้างกระทู้เรียบร้อย']);
} catch (Exception $e) {
    echo json_encode(['status'=>'error','message'=>'เกิดข้อผิดพลาดในการบันทึก']);
}
