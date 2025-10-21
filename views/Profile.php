<?php
$currentUser = $currentUser ?? [
    'username' => 'Guest',
    'email' => '',
    'bio' => '',
    'role' => 'guest'
];
?>
<div class="card bg-base-100 shadow-xl p-4 mb-4">
    <h2 class="card-title">โปรไฟล์</h2>
    <p><strong>ชื่อผู้ใช้:</strong> <?php echo htmlspecialchars($currentUser['username']); ?></p>
    <p><strong>อีเมล:</strong> <?php echo htmlspecialchars($currentUser['email'] ?? 'ไม่ระบุ'); ?></p>
    <p><strong>เกี่ยวกับ:</strong> <?php echo htmlspecialchars($currentUser['bio'] ?? 'ไม่มีข้อมูล'); ?></p>
    <p><strong>บทบาท:</strong> <?php echo htmlspecialchars($currentUser['role']); ?></p>
    <?php if ($currentUser['role'] !== 'guest'): ?>
        <a href="?action=edit-profile" class="btn btn-sm btn-outline mt-2">แก้ไขโปรไฟล์</a>
    <?php endif; ?>
</div>