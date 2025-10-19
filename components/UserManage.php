<?php
// components/UserManage.php

if ($currentUser['role'] !== 'admin')
    return;

$userList = $users ?? [];
?>

<div class="card bg-white p-6 mb-4 shadow rounded mx-auto w-full">
    <h2 class="text-xl font-bold mb-4">จัดการผู้ใช้</h2>

    <?php if (empty($userList)): ?>
        <p class="text-gray-500">ยังไม่มีผู้ใช้</p>
    <?php else: ?>
        <table class="table-auto w-full border-collapse border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2 text-left">ID</th>
                    <th class="border px-4 py-2 text-left">ชื่อผู้ใช้</th>
                    <th class="border px-4 py-2 text-left">อีเมล</th>
                    <th class="border px-4 py-2 text-left">บทบาท</th>
                    <th class="border px-4 py-2 text-left">สมาชิกตั้งแต่</th>
                    <th class="border px-4 py-2 text-left">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userList as $u):
                    $joinedAt = isset($u['created_at']) ? date('d M Y', strtotime($u['created_at'])) : '-';
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2"><?= $u['id']; ?></td>
                        <td class="border px-4 py-2"><?= htmlspecialchars($u['username']); ?></td>
                        <td class="border px-4 py-2"><?= htmlspecialchars($u['email']); ?></td>
                        <td class="border px-4 py-2"><?= htmlspecialchars($u['role']); ?></td>
                        <td class="border px-4 py-2"><?= $joinedAt; ?></td>
                        <td class="border px-4 py-2 space-x-2">
                            <!-- <a href="?action=edit-user&user=<?= $u['id']; ?>" class="btn btn-sm btn-outline">แก้ไข</a> -->
                            <a href="?action=delete-user&user=<?= $u['id']; ?>" class="btn btn-sm btn-error"
                                onclick="return confirm('คุณต้องการลบผู้ใช้นี้จริงหรือไม่?')">ลบ</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>