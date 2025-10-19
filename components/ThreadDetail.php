<?php
if (!$currentThread) return; // ไม่มี thread ไม่ต้องแสดงอะไร

// --- ตรวจสอบว่าผู้ใช้กดไลค์แล้วหรือยัง ---
$hasLiked = false;
if ($currentUser) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE thread_id=? AND user_id=?");
    $stmt->execute([$currentThread['id'], $currentUser['id']]);
    $hasLiked = $stmt->fetchColumn() > 0;
}

// --- Thread Info ---
$title = htmlspecialchars($currentThread['title'] ?? 'ไม่ระบุ');
$authorId = $currentThread['user_id'] ?? null;
$author = $userMap[$authorId] ?? ['username' => 'ไม่ระบุ'];
$categoryId = $currentThread['category_id'] ?? null;
$categoryName = $categoryMap[$categoryId] ?? 'ไม่ระบุ';
$createdAt = isset($currentThread['created_at']) ? date('d M Y H:i', strtotime($currentThread['created_at'])) : 'ไม่ระบุ';

// --- Fetch Comments ---
$threadComments = array_filter($comments, fn($c) => ($c['thread_id'] ?? 0) == $currentThread['id']);

// --- Count Likes ---
$likeCount = count(array_filter($likes, fn($l) => ($l['thread_id'] ?? 0) == $currentThread['id']));
?>

<div class="card bg-white p-6 shadow rounded mb-6">
    <a href="./index.php" class="p-2 mb-4 hover:bg-gray-100">&lt; กลับ</a>

    <h2 class="text-2xl font-bold mb-2"><?= $title; ?></h2>
    <div class="flex justify-between text-gray-500 text-sm mb-4">
        <span>โดย: <?= htmlspecialchars($author['username']); ?></span>
        <span>หมวด: <?= htmlspecialchars($categoryName); ?></span>
        <span>สร้าง: <?= $createdAt; ?></span>
    </div>

    <div class="thread-content flex gap-4 mb-4">
        <?= nl2br(htmlspecialchars($currentThread['content'] ?? '')); ?>
    </div>

    <div class="flex gap-4 mb-4 text-gray-600 text-sm">
        <span>ไลค์: <?= $likeCount; ?></span>
        <?php if ($currentUser): ?>
            <?php if ($hasLiked): ?>
                <button class="btn btn-sm btn-secondary" disabled>กดไลค์แล้ว</button>
            <?php else: ?>
                <a href="?thread=<?= $currentThread['id']; ?>&action=like" class="btn btn-sm btn-dash btn-secondary">กดไลค์</a>
            <?php endif; ?>
            <a href="?thread=<?= $currentThread['id']; ?>&action=report" class="btn btn-sm btn-error">รายงาน</a>
        <?php endif; ?>
    </div>

    <div class="mt-6">
        <h3 class="font-semibold mb-2">คอมเมนต์ (<?= count($threadComments); ?>)</h3>

        <?php if ($currentUser): ?>
            <?php if (!empty($commentError)): ?>
                <p class="text-red-500 mb-2"><?= htmlspecialchars($commentError); ?></p>
            <?php endif; ?>
            <form method="POST" class="mb-4">
                <textarea name="content" placeholder="เขียนคอมเมนต์..." class="textarea w-full mb-2" required></textarea>
                <button type="submit" name="comment" class="btn btn-sm btn-primary">โพสต์คอมเมนต์</button>
            </form>
        <?php else: ?>
            <p class="text-gray-500 mb-4">กรุณา <a href="?action=login" class="text-blue-500 underline">เข้าสู่ระบบ</a> เพื่อคอมเมนต์</p>
        <?php endif; ?>

        <?php if (empty($threadComments)): ?>
            <p class="text-gray-500">ยังไม่มีคอมเมนต์</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($threadComments as $comment): ?>
                    <?php
                    $commentUserId = $comment['author_id'] ?? null;
                    $commentUser = ($commentUserId && isset($userMap[$commentUserId]))
                        ? $userMap[$commentUserId]
                        : ['username' => 'ไม่ระบุ'];
                    $commentAt = isset($comment['created_at']) ? date('d M Y H:i', strtotime($comment['created_at'])) : 'ไม่ระบุ';
                    $content = htmlspecialchars($comment['content'] ?? '');
                    ?>
                    <div class="border rounded p-3 bg-gray-50">
                        <div class="flex justify-between text-gray-500 text-sm mb-1">
                            <span><?= htmlspecialchars($commentUser['username']); ?></span>
                            <span><?= $commentAt; ?></span>
                        </div>
                        <p><?= nl2br($content); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
