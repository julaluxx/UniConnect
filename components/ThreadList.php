<?php
// components/ThreadList.php

$categoryFilter = $_GET['category'] ?? null;
$currentThreadId = $_GET['thread'] ?? null;

// สร้าง lookup table
$userMap = [];
foreach ($users as $user) {
    $userMap[$user['id']] = $user;
}
$categoryMap = [];
foreach ($categories as $cat) {
    $categoryMap[$cat['id']] = $cat['name'] ?? 'ไม่ระบุ';
}

// เลือก thread ปัจจุบัน
$currentThread = null;
if ($currentThreadId) {
    foreach ($threads as $thread) {
        if ($thread['id'] == $currentThreadId) {
            $currentThread = $thread;
            break;
        }
    }
}

// --- ถ้ามี thread ที่เลือก ให้แสดง ThreadDetail แทน ThreadList ---
if ($currentThread) {
    include 'ThreadDetail.php';
    return; // stop ไม่ต้องแสดงรายการ threads
}

// --- ถ้าไม่มี thread ที่เลือก ให้แสดงรายการ threads ปกติ ---
$displayThreads = [];
foreach ($threads as $thread) {
    if ($categoryFilter && $thread['category_id'] != $categoryFilter)
        continue;
    $displayThreads[] = $thread;
}
?>

<div class="space-y-4">
    <?php foreach ($displayThreads as $thread): ?>
        <?php
        $threadId = $thread['id'] ?? 0;
        $title = htmlspecialchars($thread['title'] ?? 'ไม่ระบุ');
        $authorId = $thread['user_id'] ?? null;
        $authorName = ($authorId && isset($userMap[$authorId])) ? $userMap[$authorId]['username'] : 'ไม่ระบุ';
        $categoryName = $categoryMap[$thread['category_id']] ?? 'ไม่ระบุ';
        $createdAt = isset($thread['created_at']) ? date('d M Y', strtotime($thread['created_at'])) : 'ไม่ระบุ';
        $commentCount = count(array_filter($comments, fn($c) => ($c['thread_id'] ?? 0) == $threadId));
        $likeCount = count(array_filter($likes, fn($l) => ($l['thread_id'] ?? 0) == $threadId));
        ?>
        <div class="card bg-white p-4 shadow rounded hover:shadow-md transition">
            <a href="?thread=<?php echo $threadId; ?><?php echo $categoryFilter ? '&category=' . $categoryFilter : ''; ?>"
                class="text-lg font-semibold hover:text-blue-500">
                <?php echo $title; ?>
            </a>
            <div class="flex justify-between text-gray-500 text-sm mt-1">
                <span>โดย: <?php echo htmlspecialchars($authorName); ?></span>
                <span>หมวด: <?php echo htmlspecialchars($categoryName); ?></span>
                <span>สร้าง: <?php echo $createdAt; ?></span>
            </div>
            <div class="flex gap-4 text-gray-400 text-sm mt-2">
                <span>คอมเมนต์: <?php echo $commentCount; ?></span>
                <span>ไลค์: <?php echo $likeCount; ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>