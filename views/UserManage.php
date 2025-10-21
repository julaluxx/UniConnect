<?php
$currentUser = $data['currentUser'] ?? ['role' => 'guest', 'id' => 0];
$users = $data['users'] ?? [];
?>
<div class="card bg-base-100 shadow-xl p-4">
    <h2 class="card-title">จัดการผู้ใช้</h2>
    <?php if (empty($users)): ?>
        <p>ไม่มีผู้ใช้</p>
    <?php else: ?>
        <table class="table w-full">
            <thead>
                <tr>
                    <th>ชื่อผู้ใช้</th>
                    <th>อีเมล</th>
                    <th>บทบาท</th>
                    <th>การกระทำ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <a href="?action=edit-user&user=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-sm btn-primary">แก้ไข</a>
                            <a href="?action=delete-user&user=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-sm btn-error">ลบ</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>