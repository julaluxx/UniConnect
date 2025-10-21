<?php
$currentUser = $data['currentUser'] ?? ['role' => 'guest', 'id' => 0];
$threads = $data['threads'] ?? [];
$users = $data['users'] ?? [];
$categories = $data['categories'] ?? [];
$reports = $data['reports'] ?? [];

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
    <h2 class="card-title">จัดการกระทู้</h2>
    <?php if (empty($threads)): ?>
        <p>ไม่มีกระทู้</p>
    <?php else: ?>
        <table class="table w-full">
            <thead>
                <tr>
                    <th>หัวข้อ</th>
                    <th>ผู้เขียน</th>
                    <th>หมวดหมู่</th>
                    <th>วันที่</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($threads as $thread): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($thread['title']); ?></td>
                        <td><?php echo htmlspecialchars(findUserById($users, $thread['author_id'])['username']); ?></td>
                        <td><?php echo htmlspecialchars(findCategoryById($categories, $thread['category_id'])['name']); ?></td>
                        <td><?php echo htmlspecialchars($thread['created_at']); ?></td>
                        <td>
                            <a href="?action=delete-thread&thread=<?php echo htmlspecialchars($thread['id']); ?>" class="btn btn-sm btn-error">ลบ</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>