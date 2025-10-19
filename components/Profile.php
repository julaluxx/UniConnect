<?php
// components/Profile.php

// ตรวจสอบว่ามีผู้ใช้ login หรือไม่
if ($currentUser['role'] === 'guest') {
    echo '<div class="card bg-white p-4 mb-4 shadow rounded text-center">';
    echo '<p class="text-gray-500">กรุณา <a href="?action=login" class="text-blue-500 underline">เข้าสู่ระบบ</a> เพื่อดูโปรไฟล์</p>';
    echo '</div>';
    return;
}

// กำหนดค่า default ถ้าไม่มีข้อมูล
$username = htmlspecialchars($currentUser['username'] ?? 'ไม่ระบุ');
$email = htmlspecialchars($currentUser['email'] ?? 'ไม่ระบุ');
$avatar = htmlspecialchars($currentUser['profile_image'] ?? './assets/square_holder.png');
$bio = htmlspecialchars($currentUser['bio'] ?? 'สวัสดี');
$joinedAt = isset($currentUser['created_at']) ? date('d M Y', strtotime($currentUser['created_at'])) : 'ไม่ระบุ';
$role = htmlspecialchars($currentUser['role'] ?? 'user');
?>

<div class="card bg-white p-4 mb-4 shadow rounded">
    <div class="flex flex-col items-center">
        <!-- Avatar -->
        <img src="<?= $avatar; ?>" alt="Avatar"
            class="w-24 h-24 rounded-full mb-3 object-cover border-2 border-primary">

        <!-- Username & Role -->
        <h3 class="text-xl font-bold"><?= $username; ?></h3>
        <span class="text-sm text-gray-500 mb-2"><?= ucfirst($role); ?></span>

        <!-- Email -->
        <p class="text-gray-600 mb-2"><?= $email; ?></p>

        <!-- Bio -->
        <p class="text-gray-700 text-center mb-2"><?= $bio; ?></p>

        <!-- Joined Date -->
        <p class="text-gray-400 text-sm mb-4">สมาชิกตั้งแต่: <?= $joinedAt; ?></p>

        <!-- Edit Profile Button -->
        <a href="?action=edit-profile" class="btn btn-outline btn-sm">แก้ไขโปรไฟล์</a>
    </div>
</div>