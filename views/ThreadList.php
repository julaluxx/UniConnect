<?php
$currentUser = $data['currentUser'] ?? ['role' => 'guest', 'id' => 0];
$filteredThreads = $data['filteredThreads'] ?? [];
$allData = $data['allData'] ?? ['threads' => [], 'users' => [], 'categories' => []];

function findUserById($users, $id) {
    foreach ($users as $user) {
        if ($user['id'] == $id) {
            return $user;
        }
    }
    return ['username' => 'ไม่ระบุ'];
}

function findCategoryById($categories, $id) {
    foreach ($categories as $category) {
        if ($category['id'] == $id) {
            return $category;
        }
    }
    return ['name' => 'ไม่ระบุ'];
}
?>
<div class="card bg-base-100 shadow-xl p-4">
    <h2 class="card-title">กระทู้ทั้งหมด</h2>
    <?php if (!is_array($filteredThreads) || empty($filteredThreads)): ?>
        <p>ไม่พบกระทู้</p>
    <?php else: ?>
        <?php foreach ($filteredThreads as $thread): ?>
            <div class="card bg-base-200 p-4 mb-2">
                <h3 class="font-bold"><a href="?thread=<?php echo htmlspecialchars($thread['id']); ?>" class="link link-primary"><?php echo htmlspecialchars($thread['title']); ?></a></h3>
                <p><?php echo htmlspecialchars(substr($thread['content'], 0, 100)) . (strlen($thread['content']) > 100 ? '...' : ''); ?></p>
                <p class="text-sm">
                    โดย: <?php echo htmlspecialchars(findUserById($allData['users'], $thread['author_id'])['username']); ?> | 
                    หมวดหมู่: <?php echo htmlspecialchars(findCategoryById($allData['categories'], $thread['category_id'])['name']); ?> | 
                    วันที่: <?php echo htmlspecialchars($thread['created_at']); ?>
                </p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>