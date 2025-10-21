<?php
$currentUser = $currentUser ?? ['role' => 'guest', 'id' => 0];
$threadId = $threadId ?? null;

// ฟังก์ชันช่วยค้นหากระทู้เดี่ยว
function findThreadById($threads, $threadId)
{
    foreach ($threads as $thread) {
        if ($thread['id'] == $threadId) {
            return $thread;
        }
    }
    return null;
}

// ฟังก์ชันช่วยค้นหาข้อมูลผู้ใช้และหมวดหมู่ (จาก ThreadList.php)
function findUserById($users, $id)
{
    foreach ($users as $user) {
        if ($user['id'] == $id) {
            return $user;
        }
    }
    return ['username' => 'ไม่ระบุ'];
}

function findCategoryById($categories, $id)
{
    foreach ($categories as $category) {
        if ($category['id'] == $id) {
            return $category;
        }
    }
    return ['name' => 'ไม่ระบุ'];
}

// ค้นหากระทู้
$thread = findThreadById($allData['threads'] ?? [], $threadId);
if (!$thread) {
    echo "<div class='alert alert-error'>ไม่พบกระทู้นี้</div>";
    return;
}

// คำนวณจำนวนไลค์
$likeCount = count(array_filter($allData['likes'] ?? [], fn($l) => $l['thread_id'] == $threadId));

// ตรวจสอบว่าไลค์แล้วหรือไม่
$hasLiked = false;
foreach ($allData['likes'] ?? [] as $like) {
    if ($like['thread_id'] == $threadId && $like['user_id'] == $currentUser['id']) {
        $hasLiked = true;
        break;
    }
}

// ความคิดเห็น
$threadComments = array_filter($allData['comments'] ?? [], fn($c) => $c['thread_id'] == $threadId);
?>
<div class="card bg-base-100 shadow-xl p-4">
    <h2 class="card-title"><?php echo htmlspecialchars($thread['title']); ?></h2>
    <p class="text-sm mb-4">
        โดย: <?php echo htmlspecialchars(findUserById($allData['users'] ?? [], $thread['author_id'])['username']); ?> |
        หมวดหมู่: <?php echo htmlspecialchars(findCategoryById($allData['categories'] ?? [], $thread['category_id'])['name']); ?> |
        วันที่: <?php echo htmlspecialchars($thread['created_at']); ?>
    </p>
    <p class="mb-4"><?php echo nl2br(htmlspecialchars($thread['content'])); ?></p>
    <div class="flex items-center mb-4">
        <span class="mr-2">ถูกใจ: <?php echo $likeCount; ?></span>
        <?php if ($currentUser['role'] !== 'guest'): ?>
            <a href="?action=like-toggle&thread=<?php echo htmlspecialchars($threadId); ?>" class="btn btn-sm btn-outline mr-2">
                <?php echo $hasLiked ? 'ยกเลิกถูกใจ' : 'ถูกใจ'; ?>
            </a>
            <a href="?action=report&thread=<?php echo htmlspecialchars($threadId); ?>" class="btn btn-sm btn-warning">รายงานกระทู้</a>
        <?php endif; ?>
    </div>
    <h3 class="font-bold mb-2">ความคิดเห็น</h3>
    <?php if (empty($threadComments)): ?>
        <p>ยังไม่มีความคิดเห็น</p>
    <?php else: ?>
        <?php foreach ($threadComments as $comment): ?>
            <div class="card bg-base-200 p-2 mb-2">
                <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                <p class="text-sm">
                    โดย: <?php echo htmlspecialchars(findUserById($allData['users'] ?? [], $comment['author_id'])['username']); ?> |
                    วันที่: <?php echo htmlspecialchars($comment['created_at']); ?>
                </p>
                <?php if ($currentUser['role'] !== 'guest'): ?>
                    <a href="?action=report-comment&comment=<?php echo htmlspecialchars($comment['id']); ?>&thread=<?php echo htmlspecialchars($threadId); ?>" class="btn btn-xs btn-warning">รายงานคอมเมนต์</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if ($currentUser['role'] !== 'guest'): ?>
        <form method="POST" action="?thread=<?php echo htmlspecialchars($threadId); ?>" class="mt-4">
            <input type="hidden" name="comment" value="1">
            <textarea name="content" class="textarea textarea-bordered w-full" placeholder="เพิ่มความคิดเห็น" required></textarea>
            <button type="submit" class="btn btn-primary mt-2">ส่งความคิดเห็น</button>
        </form>
    <?php endif; ?>
</div>