<?php
// components/Profile.php
// ตรวจสอบว่ามีผู้ใช้ login หรือไม่
if (!$currentUser) {
    echo '<div class="card bg-white p-4 mb-4 shadow rounded text-center">';
    echo '<p class="text-gray-500">กรุณา <a href="?action=login" class="text-blue-500 underline">เข้าสู่ระบบ</a> เพื่อดูโปรไฟล์</p>';
    echo '</div>';
    return;
}

// กำหนดค่า default ถ้าไม่มีข้อมูล
$username = htmlspecialchars($currentUser['username'] ?? 'ไม่ระบุ');
$email = htmlspecialchars($currentUser['email'] ?? 'ไม่ระบุ');
$avatar = htmlspecialchars($currentUser['avatar'] ?? 'https://via.placeholder.com/150');
$joinedAt = isset($currentUser['created_at']) ? date('d M Y', strtotime($currentUser['created_at'])) : 'ไม่ระบุ';
?>

<div class="card bg-white p-4 mb-4 shadow rounded">
    <div class="flex flex-col items-center">
        <!-- Username -->
        <h3 class="card-title text-xl font-bold mb-1"><?= $username; ?></h3>

        <!-- Avatar -->
        <img src="<?= $avatar; ?>" alt="Avatar" class="w-24 h-24 rounded-full mb-4">

        <div class="card-body items-center">

            <!-- Email -->
            <p class="text-gray-500 mb-2"><?= $email; ?></p>

            <!-- Bio -->
            <p>
                <?= $currentUser['bio']; ?>
            </p>

            <!-- Joined Date -->
            <p class="text-gray-400 text-sm">สมาชิกตั้งแต่: <?= $joinedAt; ?></p>

        </div>

        <!-- Edit Profile -->
        <div class="btn btn-outline">
            <a href="?action=edit-profile">แก้ไขโปรไฟล์</a>
        </div>
    </div>
</div>