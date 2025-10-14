<?php
require 'config.php';

$thread_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($thread_id === 0) {
    die("Invalid thread ID");
}

$stmt = $pdo->prepare("SELECT t.title, t.content, u.username as author, t.created_at as timestamp 
                       FROM threads t JOIN users u ON t.author_id = u.id WHERE t.id = ?");
$stmt->execute([$thread_id]);
$thread = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$thread) {
    die("Thread not found");
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($thread['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="thread" data-thread-id="<?php echo $thread_id; ?>">
            <div id="thread-title" class="card-title"><?php echo htmlspecialchars($thread['title']); ?></div>
            <div class="card-body">
                <div id="thread-content"><?php echo nl2br(htmlspecialchars($thread['content'])); ?></div>
                <div id="thread-author">โพสต์โดย <?php echo htmlspecialchars($thread['author']); ?></div>
                <div id="thread-timestamp"><?php echo $thread['timestamp']; ?></div>
            </div>
        </div>
        <a href="index.php" class="btn btn-primary">กลับสู่หน้าแรก</a>
    </div>
</body>
</html>