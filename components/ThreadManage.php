<?php
if ($currentUser['role'] !== 'admin')
    return;

// เตรียมข้อมูล threads
$threadList = $threads ?? [];
?>

<div class="card bg-white p-6 mb-4 shadow rounded mx-auto w-full">
    <h2 class="text-xl font-bold mb-4">จัดการกระทู้</h2>

    <?php if (empty($threadList)): ?>
        <p class="text-gray-500">ยังไม่มีกระทู้</p>
    <?php else: ?>
        <table class="table-auto w-full border-collapse border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2 text-left">ID</th>
                    <th class="border px-4 py-2 text-left">หัวข้อ</th>
                    <th class="border px-4 py-2 text-left">ผู้สร้าง</th>
                    <th class="border px-4 py-2 text-left">หมวดหมู่</th>
                    <th class="border px-4 py-2 text-left">วันที่สร้าง</th>
                    <th class="border px-4 py-2 text-left">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($threadList as $t):
                    // หาผู้สร้าง
                    $authorName = 'ไม่ระบุ';
                    foreach ($users as $u) {
                        if ($u['id'] == ($t['author_id'] ?? 0)) {
                            $authorName = $u['username'];
                            break;
                        }
                    }
                    // หาหมวดหมู่
                    $categoryName = 'ไม่ระบุ';
                    foreach ($categories as $c) {
                        if ($c['id'] == ($t['category_id'] ?? 0)) {
                            $categoryName = $c['name'];
                            break;
                        }
                    }
                    $createdAt = isset($t['created_at']) ? date('d M Y', strtotime($t['created_at'])) : '-';
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2"><?= $t['id']; ?></td>
                        <td class="border px-4 py-2"><?= htmlspecialchars($t['title']); ?></td>
                        <td class="border px-4 py-2"><?= htmlspecialchars($authorName); ?></td>
                        <td class="border px-4 py-2"><?= htmlspecialchars($categoryName); ?></td>
                        <td class="border px-4 py-2"><?= $createdAt; ?></td>
                        <td class="border px-4 py-2 space-x-2">
                            <!-- <a href="?action=edit-thread&thread=<?= $t['id']; ?>" class="btn btn-sm btn-outline">แก้ไข</a> -->
                            <a href="?action=delete-thread&thread=<?= $t['id']; ?>" class="btn btn-sm btn-error"
                                onclick="return confirm('คุณต้องการลบกระทู้นี้จริงหรือไม่?')">ลบ</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>