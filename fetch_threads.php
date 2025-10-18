<?php
// fetch_threads.php
require 'db.php';
header('Content-Type: application/json');

try {
    $stmt = $conn->query("
        SELECT t.id, t.title, t.content, t.created_at, u.username AS author_name, c.name AS category_name,
               (SELECT COUNT(*) FROM comments WHERE thread_id = t.id) AS comment_count,
               (SELECT COUNT(*) FROM likes WHERE thread_id = t.id) AS like_count
        FROM threads t
        JOIN users u ON t.author_id = u.id
        JOIN categories c ON t.category_id = c.id
        ORDER BY t.created_at DESC
    ");
    $threads = $stmt->fetchAll();
    echo json_encode(['status'=>'success','threads'=>$threads]);
} catch (Exception $e) {
    echo json_encode(['status'=>'error','message'=>'ไม่สามารถดึงข้อมูลได้']);
}
