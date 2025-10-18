<?php
require 'pdo.php';
header('Content-Type: application/json');

$stmt = $pdo->query("
    SELECT t.id, t.title, t.content, t.created_at, u.username, c.name AS category
    FROM threads t
    JOIN users u ON t.user_id = u.id
    JOIN categories c ON t.category_id = c.id
    ORDER BY t.created_at DESC
");
$threads = $stmt->fetchAll();

echo json_encode($threads);
?>
