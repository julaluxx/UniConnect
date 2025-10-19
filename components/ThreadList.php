<?php
// components/ThreadList.php

if (empty($threads)) {
    echo '<div class="card bg-white p-4 shadow rounded">';
    echo '<p class="text-gray-500 text-center">ยังไม่มีกระทู้</p>';
    echo '</div>';
    return;
}

// สร้าง array lookup สำหรับ user และ category
$userMap = [];
foreach ($users as $user) {
    $userMap[$user['id']] = $user['username'] ?? 'ไม่ระบุ';
}

$categoryMap = [];
foreach ($categories as $cat) {
    $categoryMap[$cat['id']] = $cat['name'] ?? 'ไม่ระบุ';
}
?>

<div class="space-y-4">
    <?php foreach ($threads as $thread): ?>
        <?php
        $threadId = $thread['id'] ?? 0;
        $title = htmlspecialchars($thread['title'] ?? 'ไม่ระบุ');
        $authorId = $thread['user_id'] ?? null;
        $authorName = $userMap[$authorId] ?? 'ไม่ระบุ';
        $categoryId = $thread['category_id'] ?? null;
        $categoryName = $categoryMap[$categoryId] ?? 'ไม่ระบุ';
        $createdAt = isset($thread['created_at']) ? date('d M Y', strtotime($thread['created_at'])) : 'ไม่ระบุ';

        // นับจำนวนคอมเมนต์
        $commentCount = 0;
        foreach ($comments as $comment) {
            if (($comment['thread_id'] ?? 0) == $threadId)
                $commentCount++;
        }

        // นับจำนวนไลค์
        $likeCount = 0;
        foreach ($likes as $like) {
            if (($like['thread_id'] ?? 0) == $threadId)
                $likeCount++;
        }
        ?>
        <div class="card bg-white p-4 shadow rounded hover:shadow-md transition">
            <a href="?thread=<?php echo $threadId; ?>" class="text-lg font-semibold hover:text-blue-500">
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