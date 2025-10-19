<?php
// ThreadList.php

$categoryFilter = $_GET['category'] ?? null;
$currentThreadId = $_GET['thread'] ?? null;

// --- สร้าง lookup table สำหรับชื่อผู้ใช้และหมวดหมู่ ---
$userMap = [];
foreach ($users as $user) {
    $userMap[$user['id']] = $user;
}

$categoryMap = [];
foreach ($categories as $cat) {
    $categoryMap[$cat['id']] = $cat['name'] ?? 'ไม่ระบุ';
}

// --- เลือก thread ปัจจุบัน ---
$currentThread = null;
if ($currentThreadId) {
    foreach ($threads as $thread) {
        if ($thread['id'] == $currentThreadId) {
            $currentThread = $thread;
            break;
        }
    }
}

// --- ถ้ามี thread ที่เลือก ให้แสดง ThreadDetail ---
if ($currentThread) {
    include 'ThreadDetail.php';
    return;
}

// --- กรองและเรียงกระทู้ ---
$displayThreads = [];
foreach ($threads as $thread) {
    if ($categoryFilter && $thread['category_id'] != $categoryFilter)
        continue;
    $displayThreads[] = $thread;
}

// ✅ เรียงจากใหม่สุด → เก่าสุด
usort($displayThreads, fn($a, $b) => ($b['id'] ?? 0) <=> ($a['id'] ?? 0));
?>

<div class="space-y-4">
    <?php if (empty($displayThreads)): ?>
        <p class="text-gray-500">ยังไม่มีกระทู้ในหมวดนี้</p>
    <?php endif; ?>

    <?php foreach ($displayThreads as $thread): ?>
        <?php
        $threadId = $thread['id'];
        $title = htmlspecialchars($thread['title'] ?? 'ไม่ระบุ');
        $authorId = $thread['author_id'] ?? 0;
        $authorName = $userMap[$authorId]['username'] ?? 'ไม่ระบุ';
        $categoryName = $categoryMap[$thread['category_id']] ?? 'ไม่ระบุ';
        $createdAt = $thread['created_at'] ? date('d M Y', strtotime($thread['created_at'])) : '';
        $commentCount = count(array_filter($comments, fn($c) => ($c['thread_id'] ?? 0) == $threadId));
        $likeCount = count(array_filter($likes, fn($l) => ($l['thread_id'] ?? 0) == $threadId));
        ?>
        <div class="card bg-white p-4 shadow rounded hover:shadow-md transition">
            <a href="?thread=<?= $threadId; ?><?= $categoryFilter ? '&category=' . $categoryFilter : ''; ?>"
                class="text-lg font-semibold hover:text-blue-500">
                <?= $title; ?>
            </a>
            <div class="flex justify-between text-gray-500 text-sm mt-1">
                <span>โดย: <?= htmlspecialchars($authorName); ?></span>
                <span>หมวด: <?= htmlspecialchars($categoryName); ?></span>
                <span>สร้าง: <?= $createdAt; ?></span>
            </div>
            <div class="flex gap-4 text-gray-400 text-sm mt-2">
                <span>💬 <?= $commentCount; ?> คอมเมนต์</span>
                <span>❤️ <?= $likeCount; ?> ไลค์</span>
            </div>
        </div>
    <?php endforeach; ?>
</div>
