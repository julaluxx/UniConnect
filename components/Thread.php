<?php
// thread.php
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

<!-- Thread card -->
<div class="card bg-base-100 shadow-md mb-6">
    <div class="card-body">
        <h2 class="card-title text-xl"><?= htmlspecialchars($thread['title']) ?></h2>
        <p class="text-sm text-gray-500 mb-2">
            By <?= htmlspecialchars($thread['username']) ?> |
            Category: <?= htmlspecialchars($thread['category_name']) ?>
        </p>
        <p class="mb-4"><?= nl2br(htmlspecialchars($thread['content'])) ?></p>

        <!-- Likes & actions -->
        <div class="flex items-center gap-4">
            <p class="text-sm">👍 Likes:
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE thread_id=?");
                $stmt->execute([$thread['id']]);
                echo $stmt->fetchColumn();
                ?>
            </p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="like_action.php?thread_id=<?= $thread['id'] ?>" class="btn btn-sm btn-primary">Like/Unlike</a>

                <!-- Edit/Delete -->
                <?php if ($_SESSION['user_id'] == $thread['author_id']): ?>
                    <a href="edit_thread.php?id=<?= $thread['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
                    <a href="delete_thread.php?id=<?= $thread['id'] ?>" class="btn btn-sm btn-error"
                        onclick="return confirm('Delete this thread?')">Delete</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Comments -->
<div class="mb-6">
    <h3 class="text-lg font-semibold mb-3">💬 Comments</h3>
    <?php foreach ($comments as $c): ?>
        <div class="card bg-base-200 mb-3">
            <div class="card-body py-3 px-4">
                <p class="text-sm text-gray-600 font-semibold"><?= htmlspecialchars($c['username']) ?>:</p>
                <p><?= nl2br(htmlspecialchars($c['content'])) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Add comment form -->
<div class="mb-10">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title text-md">Add a Comment</h3>
                <form method="post">
                    <textarea name="content" class="textarea textarea-bordered w-full mb-2"
                        placeholder="Write your comment..." required></textarea>
                    <button type="submit" class="btn btn-success btn-sm">Post Comment</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <p><a href="login.php" class="link link-primary">Login</a> to comment.</p>
    <?php endif; ?>
</div>

<!-- Report form -->
<?php if (isset($_SESSION['user_id'])): ?>
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <h3 class="card-title text-md">Report Thread</h3>
            <form method="post" action="report_action.php">
                <input type="hidden" name="thread_id" value="<?= $thread['id'] ?>">
                <textarea name="description" placeholder="Describe the issue" class="textarea textarea-bordered w-full mb-2"
                    required></textarea>
                <button type="submit" class="btn btn-error btn-sm">Submit Report</button>
            </form>
        </div>
    </div>
<?php endif; ?>