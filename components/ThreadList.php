<div class="thread-list">
    <?php if (empty($threads)): ?>
        <p>ไม่มีกระทู้ในขณะนี้</p>
    <?php else: ?>
        <?php foreach ($threads as $thread): ?>
            <div class="thread card bg-base-100 shadow-md mb-4 p-4">
                <h4 class="card-title"><?php echo htmlspecialchars($thread['title']); ?></h4>
                <p>หมวดหมู่: <?php echo htmlspecialchars($thread['category_name']); ?></p>
                <p>โดย: <?php echo htmlspecialchars($thread['username']); ?></p>
                <p><a href="index.php?action=view_thread&id=<?php echo $thread['id']; ?>"
                        class="link link-hover text-primary">ดูเพิ่ม</a></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php
    if (isset($_GET['action']) && $_GET['action'] === 'view_thread' && isset($_GET['id'])) {
        $thread_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        $thread_details = null;

        if ($thread_id !== false) {
            // ดึงข้อมูลกระทู้จากฐานข้อมูล
            require 'pdo.php';
            $stmt = $conn->prepare("
                SELECT t.*, c.name AS category_name, u.username 
                FROM threads t
                JOIN categories c ON t.category_id = c.id
                JOIN users u ON t.author_id = u.id
                WHERE t.id = ?
            ");
            $stmt->execute([$thread_id]);
            $thread_details = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($thread_details): ?>
            <div class="thread-details card bg-base-100 shadow-md p-4 mt-4">
                <h3 class="card-title"><?php echo htmlspecialchars($thread_details['title']); ?></h3>
                <p>หมวดหมู่: <?php echo htmlspecialchars($thread_details['category_name']); ?></p>
                <p>โดย: <?php echo htmlspecialchars($thread_details['username']); ?></p>
                <p>วันที่สร้าง: <?php echo htmlspecialchars($thread_details['created_at']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($thread_details['content'])); ?></p>
                <p><a href="index.php" class="link link-hover text-primary">กลับสู่รายการกระทู้</a></p>
            </div>
        <?php else: ?>
            <?php
            $_SESSION['error'] = 'ไม่พบกระทู้ที่ระบุ';
            header('Location: index.php');
            exit();
            ?>
        <?php endif; ?>
    <?php } ?>
</div>