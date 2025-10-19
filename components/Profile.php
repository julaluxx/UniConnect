<?php
// components/Profile.php

if ($currentUser['role'] === 'guest'):
    ?>
    <div class="card bg-white p-4 mb-4 shadow rounded text-center">
        <p class="text-gray-500">
            กรุณา <a href="?action=login" class="text-blue-500 underline">เข้าสู่ระบบ</a> เพื่อดูโปรไฟล์
        </p>
    </div>
    <?php
    return;
endif;

$username = htmlspecialchars($currentUser['username'] ?? 'ไม่ระบุ');
$email = htmlspecialchars($currentUser['email'] ?? 'ไม่ระบุ');
$bio = htmlspecialchars($currentUser['bio'] ?? 'สวัสดี');
$joinedAt = isset($currentUser['created_at']) ? date('d M Y', strtotime($currentUser['created_at'])) : 'ไม่ระบุ';
$role = htmlspecialchars($currentUser['role'] ?? 'user');
?>

<div class="card bg-white p-4 mb-4 shadow rounded">
    <div class="flex flex-col items-center">
        <h3 class="text-xl font-bold"><?= $username; ?></h3>
        <span class="text-sm text-gray-500 mb-2"><?= ucfirst($role); ?></span>
        <p class="text-gray-600 mb-2"><?= $email; ?></p>
        <p class="text-gray-700 text-center mb-2"><?= $bio; ?></p>
        <p class="text-gray-400 text-sm mb-4">สมาชิกตั้งแต่: <?= $joinedAt; ?></p>
        <a href="?action=edit-profile" class="btn btn-outline btn-sm">แก้ไขโปรไฟล์</a>

        <?php if ($currentUser['role'] === 'admin') { ?>
            <a class="btn btn-outline btn-sm mt-2" href="?action=manage-thread">จัดการกระทู้</a>
            <a class="btn btn-outline btn-sm mt-2" href="?action=manage-user">จัดการผู้ใช้</a>
        <?php } ?>
    </div>
</div>