<?php
$users = $allData['users'] ?? [];
?>
<div class="card bg-base-100 shadow-xl p-4 mb-4">
    <h2 class="card-title">จัดการผู้ใช้</h2>
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
                        <form method="POST" action="?action=edit-user&user=<?php echo $user['id']; ?>" class="inline">
                            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="input input-sm input-bordered mr-1" required>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="input input-sm input-bordered mr-1" required>
                            <select name="role" class="select select-sm select-bordered mr-1">
                                <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                <option value="moderator" <?php echo $user['role'] === 'moderator' ? 'selected' : ''; ?>>Moderator</option>
                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">บันทึก</button>
                        </form>
                        <a href="?action=delete-user&user=<?php echo $user['id']; ?>" class="btn btn-sm btn-error" onclick="return confirm('แน่ใจหรือไม่ว่าต้องการลบผู้ใช้นี้?')">ลบ</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>