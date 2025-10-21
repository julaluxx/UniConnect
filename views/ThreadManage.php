<?php
$threads = $allData['threads'] ?? [];
?>
<div class="card bg-base-100 shadow-xl p-4 mb-4">
    <h2 class="card-title">จัดการกระทู้</h2>
    <table class="table w-full">
        <thead>
            <tr>
                <th>ชื่อกระทู้</th>
                <th>หมวดหมู่</th>
                <th>ผู้เขียน</th>
                <th>วันที่สร้าง</th>
                <th>การกระทำ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($threads as $thread): ?>
                <tr>
                    <td><?php echo htmlspecialchars($thread['title']); ?></td>
                    <td><?php echo htmlspecialchars($allData['categories'][$thread['category_id'] - 1]['name'] ?? 'ไม่ระบุ'); ?></td>
                    <td><?php echo htmlspecialchars($allData['users'][$thread['author_id'] - 1]['username'] ?? 'ไม่ระบุ'); ?></td>
                    <td><?php echo htmlspecialchars($thread['created_at']); ?></td>
                    <td>
                        <a href="?thread=<?php echo $thread['id']; ?>" class="btn btn-sm btn-info">ดู</a>
                        <a href="?action=delete-thread&thread=<?php echo $thread['id']; ?>" class="btn btn-sm btn-error" onclick="return confirm('แน่ใจหรือไม่ว่าต้องการลบกระทู้นี้?')">ลบ</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>