<?php
session_start();
require 'pdo.php';
$thread_id = $_GET['id'] ?? 0;

// ดึงกระทู้
$stmt = $conn->prepare("SELECT t.*, u.username, c.name AS category_name FROM threads t 
    JOIN users u ON t.author_id=u.id 
    JOIN categories c ON t.category_id=c.id 
    WHERE t.id=?");
$stmt->execute([$thread_id]);
$thread = $stmt->fetch();

// ดึงคอมเมนต์
$stmt = $conn->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.author_id=u.id WHERE c.thread_id=? ORDER BY c.created_at ASC");
$stmt->execute([$thread_id]);
$comments = $stmt->fetchAll();

// เพิ่มคอมเมนต์
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("INSERT INTO comments (content,thread_id,author_id) VALUES (?,?,?)");
    $stmt->execute([$_POST['content'], $thread_id, $_SESSION['user_id']]);
    header("Location: thread.php?id=$thread_id");
    exit;
}
?>
<h2><?= htmlspecialchars($thread['title']) ?></h2>
<p>By <?= htmlspecialchars($thread['username']) ?> | Category: <?= htmlspecialchars($thread['category_name']) ?></p>
<p><?= nl2br(htmlspecialchars($thread['content'])) ?></p>

<?php if(isset($_SESSION['user_id'])): ?>
    <a href="like_action.php?thread_id=<?=$thread['id']?>">Like/Unlike</a>
<?php endif; ?>


<h3>Comments</h3>
<?php foreach ($comments as $c): ?>
    <div style="border-top:1px solid #ccc; padding:5px;">
        <b><?= htmlspecialchars($c['username']) ?></b>: <?= nl2br(htmlspecialchars($c['content'])) ?>
    </div>
<?php endforeach; ?>

<?php
$stmt = $conn->prepare("SELECT COUNT(*) AS likes FROM likes WHERE thread_id=?");
$stmt->execute([$thread['id']]);
$like_count = $stmt->fetchColumn();
echo "<p>Likes: $like_count</p>";
?>

<?php if (isset($_SESSION['user_id'])): ?>
    <form method="post">
        <textarea name="content" required></textarea><br>
        <button type="submit">Add Comment</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Login</a> to comment</p>
<?php endif; ?>
<form method="post" action="report_action.php">
    <input type="hidden" name="thread_id" value="<?= $thread['id'] ?>">
    <textarea name="description" placeholder="Report this thread" required></textarea><br>
    <button type="submit">Report</button>
</form>
<?php if ($_SESSION['user_id'] == $thread['author_id']): ?>
    <a href="edit_thread.php?id=<?= $thread['id'] ?>">Edit</a> |
    <a href="delete_thread.php?id=<?= $thread['id'] ?>" onclick="return confirm('Delete this thread?')">Delete</a>
<?php endif; ?>