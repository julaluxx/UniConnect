<?php
// components/ThreadDetail.php

if (!$currentThread)
    return; // ถ้าไม่มี thread ที่เลือก ไม่ต้องแสดงอะไร

// ข้อมูล thread
$title = htmlspecialchars($currentThread['title'] ?? 'ไม่ระบุ');
$authorId = $currentThread['user_id'] ?? null;
$author = $userMap[$authorId] ?? ['username' => 'ไม่ระบุ'];
$categoryId = $currentThread['category_id'] ?? null;
$categoryName = $categoryMap[$categoryId] ?? 'ไม่ระบุ';
$createdAt = isset($currentThread['created_at']) ? date('d M Y H:i', strtotime($currentThread['created_at'])) : 'ไม่ระบุ';

// คอมเมนต์ของ thread นี้
$threadComments = array_filter($comments, fn($c) => ($c['thread_id'] ?? 0) == $currentThread['id']);

// จำนวนไลค์
$likeCount = count(array_filter($likes, fn($l) => ($l['thread_id'] ?? 0) == $currentThread['id']));
?>

<div class="card bg-white p-6 shadow rounded mb-6">
    <a href="./index.php" class="p-2 mb-4 hover:bg-gray-100">&lt; กลับ</a>

    <h2 class="text-2xl font-bold mb-2"><?php echo $title; ?></h2>
    <div class="flex justify-between text-gray-500 text-sm mb-4">
        <span>โดย: <?php echo htmlspecialchars($author['username']); ?></span>
        <span>หมวด: <?php echo htmlspecialchars($categoryName); ?></span>
        <span>สร้าง: <?php echo $createdAt; ?></span>
    </div>

    <!-- Thread content -->
    <div class="thread-content flex gap-4 mb-4">
        <?php echo $currentThread['content']; ?>
    </div>

    <!-- Like & Report -->
    <div class="flex gap-4 mb-4 text-gray-600 text-sm">
        <span>ไลค์: <?php echo $likeCount; ?></span>
        <?php if ($currentUser): ?>
            <a href="action_like.php?thread=<?php echo $currentThread['id']; ?>" class="btn btn-sm btn-primary">กดไลค์</a>
            <a href="action_report.php?thread=<?php echo $currentThread['id']; ?>" class="btn btn-sm btn-error">รายงาน</a>
        <?php endif; ?>
    </div>

    <!-- Comment Section -->
    <div class="mt-6">
        <h3 class="font-semibold mb-2">คอมเมนต์ (<?php echo count($threadComments); ?>)</h3>

        <?php if ($currentUser): ?>
            <form action="action_comment.php" method="POST" class="mb-4">
                <input type="hidden" name="thread_id" value="<?php echo $currentThread['id']; ?>">
                <textarea name="content" placeholder="เขียนคอมเมนต์..." class="textarea w-full mb-2" required></textarea>
                <button type="submit" class="btn btn-sm btn-primary">โพสต์คอมเมนต์</button>
            </form>
        <?php else: ?>
            <p class="text-gray-500 mb-4">กรุณา <a href="?action=login" class="text-blue-500 underline">เข้าสู่ระบบ</a>
                เพื่อคอมเมนต์</p>
        <?php endif; ?>

        <?php if (empty($threadComments)): ?>
            <p class="text-gray-500">ยังไม่มีคอมเมนต์</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($threadComments as $comment): ?>
                    <?php
                    $commentUserId = $comment['user_id'] ?? null; // ป้องกัน undefined key
                    $commentUser = ($commentUserId && isset($userMap[$commentUserId]))
                        ? $userMap[$commentUserId]
                        : ['username' => 'ไม่ระบุ'];

                    $commentAt = isset($comment['created_at']) ? date('d M Y H:i', strtotime($comment['created_at'])) : 'ไม่ระบุ';
                    $content = htmlspecialchars($comment['content'] ?? '');
                    ?>
                    <div class="border rounded p-3 bg-gray-50">
                        <div class="flex justify-between text-gray-500 text-sm mb-1">
                            <span><?php echo htmlspecialchars($commentUser['username']); ?></span>
                            <span><?php echo $commentAt; ?></span>
                        </div>
                        <p><?php echo nl2br($content); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>