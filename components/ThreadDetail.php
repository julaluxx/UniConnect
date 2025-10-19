<?php
// ThreadDetail.php

if (!$currentThread)
    return;

// --- ตรวจสอบว่าผู้ใช้กดไลค์แล้วหรือยัง ---
$hasLiked = false; // ✅ กำหนด default
if ($currentUser['role'] !== 'guest') {
    foreach ($likes as $l) {
        if (($l['thread_id'] ?? 0) == $currentThread['id'] && ($l['user_id'] ?? 0) == $currentUser['id']) {
            $hasLiked = true;
            break;
        }
    }
}

// --- Thread Info ---
$title = htmlspecialchars($currentThread['title'] ?? 'ไม่ระบุ');
$authorId = $currentThread['user_id'] ?? null;
$author = ['username' => 'ไม่ระบุ'];
foreach ($users as $u) {
    if ($u['id'] == $authorId) {
        $author = $u;
        break;
    }
}

$categoryId = $currentThread['category_id'] ?? null;
$categoryName = 'ไม่ระบุ';
foreach ($categories as $c) {
    if ($c['id'] == $categoryId) {
        $categoryName = $c['name'];
        break;
    }
}

$createdAt = isset($currentThread['created_at'])
    ? date('d M Y H:i', strtotime($currentThread['created_at']))
    : 'ไม่ระบุ';

// --- Fetch Comments ---
$threadComments = array_filter($comments, fn($c) => ($c['thread_id'] ?? 0) == $currentThread['id']);

// --- Count Likes ---
$likeCount = count(array_filter($likes, fn($l) => ($l['thread_id'] ?? 0) == $currentThread['id']));
?>

<div class=" mx-auto bg-white rounded shadow-lg p-6 mb-8">
    <a href="./index.php" class="inline-block mb-4 text-gray-500 hover:text-blue-700">&larr; กลับหน้าหลัก</a>

    <!-- Title -->
    <h2 class="text-3xl font-extrabold mb-2 text-gray-800"><?= $title; ?></h2>

    <!-- Info Bar -->
    <div class="flex flex-wrap justify-between items-center text-sm text-gray-500 mb-6 gap-2">
        <span>โดย: <span class="font-medium"><?= htmlspecialchars($author['username']); ?></span></span>
        <span>หมวด: <span class="font-medium"><?= htmlspecialchars($categoryName); ?></span></span>
        <span>สร้าง: <?= $createdAt; ?></span>
    </div>

    <!-- Content -->
    <div class="prose max-w-full mb-6">
        <?= nl2br(htmlspecialchars($currentThread['content'] ?? '')); ?>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <span class="text-gray-600 font-medium">❤️ <?= $likeCount; ?> ไลค์</span>

        <?php if ($currentUser['role'] !== 'guest'): ?>
            <a href="?thread=<?= $currentThread['id']; ?>&action=like-toggle"
                class="btn btn-sm <?= $hasLiked ? 'btn-disabled' : 'btn-primary' ?>">
                <?= $hasLiked ? 'กดไลค์แล้ว' : 'กดไลค์' ?>
            </a>
            <a href="?thread=<?= $currentThread['id']; ?>&action=report" class="btn btn-sm btn-error">รายงาน</a>
        <?php endif; ?>
    </div>

    <!-- Comments Section -->
    <div>
        <h3 class="text-xl font-semibold mb-4">คอมเมนต์ (<?= count($threadComments); ?>)</h3>

        <?php if ($currentUser['role'] !== 'guest'): ?>
            <?php if (!empty($commentError)): ?>
                <p class="text-red-500 mb-2"><?= htmlspecialchars($commentError); ?></p>
            <?php endif; ?>
            <form method="POST" class="mb-6">
                <textarea name="content" placeholder="เขียนคอมเมนต์..." class="textarea w-full mb-2 textarea-bordered"
                    required></textarea>
                <button type="submit" name="comment" class="btn btn-primary">โพสต์คอมเมนต์</button>
            </form>
        <?php else: ?>
            <p class="text-gray-500 mb-4">
                กรุณา <a href="?action=login" class="text-blue-500 underline">เข้าสู่ระบบ</a> เพื่อคอมเมนต์
            </p>
        <?php endif; ?>

        <?php if (empty($threadComments)): ?>
            <p class="text-gray-500">ยังไม่มีคอมเมนต์</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($threadComments as $comment): ?>
                    <?php
                    $commentUserId = $comment['author_id'] ?? null;
                    $commentUser = ['username' => 'ไม่ระบุ'];
                    foreach ($users as $u) {
                        if ($u['id'] == $commentUserId) {
                            $commentUser = $u;
                            break;
                        }
                    }
                    $commentAt = isset($comment['created_at']) ? date('d M Y H:i', strtotime($comment['created_at'])) : 'ไม่ระบุ';
                    $content = htmlspecialchars($comment['content'] ?? '');
                    ?>
                    <div class="border rounded-lg p-4 bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex justify-between items-center text-gray-500 text-sm mb-2">
                            <span class="font-medium"><?= htmlspecialchars($commentUser['username']); ?></span>
                            <span><?= $commentAt; ?></span>
                        </div>
                        <p class="text-gray-700"><?= nl2br($content); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php
    // REPORT (show form)
    if ($action === 'report' && $currentUser['role'] !== 'guest') {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM reports WHERE thread_id=? AND reported_by=?");
        $stmt->execute([$threadId, $currentUser['id']]);
        $alreadyReported = $stmt->fetchColumn() > 0;

        include 'components/Report.php';
        exit;
    }
    ?>

</div>