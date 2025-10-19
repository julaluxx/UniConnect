<?php
// เมื่อกดดูรายละเอียดเธรด
if (isset($_GET['thread_id'])) {
    $thread_id = intval($_GET['thread_id']);
    $stmt = $conn->prepare("
        SELECT t.*, u.username, c.name AS category_name
        FROM threads t
        JOIN users u ON t.author_id = u.id
        JOIN categories c ON t.category_id = c.id
        WHERE t.id = ?
    ");
    $stmt->execute([$thread_id]);
    $thread = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($thread):
        ?>
        <div class="bg-white p-6 rounded shadow">
            <a href="index.php" class="text-sm text-blue-600">← กลับไปหน้าเธรดทั้งหมด</a>
            <h2 class="text-2xl font-semibold mt-3"><?= htmlspecialchars($thread['title']) ?></h2>
            <p class="text-gray-600 text-sm">หมวดหมู่: <?= htmlspecialchars($thread['category_name']) ?> | โดย
                <?= htmlspecialchars($thread['username']) ?> | <?= htmlspecialchars($thread['created_at']) ?></p>
            <hr class="my-3" />
            <p><?= nl2br(htmlspecialchars($thread['content'])) ?></p>

            <!-- ส่วนคอมเมนต์ -->
            <h3 class="text-lg font-semibold mt-6 mb-2">ความคิดเห็น</h3>
            <?php
            $comments = $conn->prepare("
      SELECT c.*, u.username 
      FROM comments c
      JOIN users u ON c.user_id = u.id
      WHERE c.thread_id = ?
      ORDER BY c.created_at ASC
  ");
            $comments->execute([$thread_id]);
            $comment_list = $comments->fetchAll(PDO::FETCH_ASSOC);

            if ($comment_list):
                foreach ($comment_list as $cmt): ?>
                    <div class="border rounded p-3 mb-2 bg-gray-50">
                        <p class="text-sm text-gray-600 mb-1"><?= htmlspecialchars($cmt['username']) ?> กล่าวว่า:</p>
                        <p><?= nl2br(htmlspecialchars($cmt['content'])) ?></p>
                        <p class="text-xs text-gray-400 mt-1"><?= htmlspecialchars($cmt['created_at']) ?></p>
                    </div>
                <?php endforeach;
            else: ?>
                <p class="text-gray-500">ยังไม่มีความคิดเห็น</p>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- ฟอร์มเพิ่มความคิดเห็น -->
                <form method="POST" class="mt-4">
                    <textarea name="comment" placeholder="แสดงความคิดเห็น..." required
                        class="textarea textarea-bordered w-full"></textarea>
                    <button type="submit" name="add_comment" class="btn btn-primary mt-2">ส่งความคิดเห็น</button>
                </form>
            <?php endif; ?>

            <?php
            if (isset($_POST['add_comment'])) {
                $comment = trim($_POST['comment']);
                $user_id = $_SESSION['user_id'];
                if ($comment !== '') {
                    $stmt = $conn->prepare("INSERT INTO comments (thread_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$thread_id, $user_id, $comment]);
                    header("Location: index.php?thread_id=$thread_id");
                    exit;
                }
            }
            ?>
        </div>

        <?php
    else:
        echo "<p>ไม่พบเธรดนี้</p>";
    endif;
} else {
    // แสดงรายการเธรดทั้งหมด
    ?>
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">กระทู้ทั้งหมด</h2>
        <?php if (empty($threads)): ?>
            <p class="text-gray-500">ยังไม่มีเธรดในระบบ</p>
        <?php else: ?>
            <?php foreach ($threads as $thread): ?>
                <div class="border-b py-3">
                    <a href="index.php?thread_id=<?= $thread['id'] ?>" class="text-lg font-semibold text-blue-600 hover:underline">
                        <?= htmlspecialchars($thread['title']) ?>
                    </a>
                    <p class="text-sm text-gray-600">
                        โดย <?= htmlspecialchars($thread['username']) ?> |
                        <?= htmlspecialchars($thread['category_name']) ?> |
                        <?= htmlspecialchars($thread['created_at']) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php } ?>