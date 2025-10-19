<?php
// fetch_threads.php
require 'pdo.php';
header('Content-Type: application/json');

try {
    $stmt = $conn->query("
        SELECT 
            t.id,
            t.title,
            t.content,
            t.created_at,
            t.updated_at,
            u.username,
            c.name AS category,

            -- นับจำนวน likes
            (SELECT COUNT(*) FROM likes l WHERE l.thread_id = t.id) AS like_count,

            -- นับจำนวน comments
            (SELECT COUNT(*) FROM comments cm WHERE cm.thread_id = t.id) AS comment_count

        FROM threads t
        JOIN users u ON t.author_id = u.id
        JOIN categories c ON t.category_id = c.id
        ORDER BY t.created_at DESC
    ");

    $threads = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $threads
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch threads'
    ]);
}
?>
