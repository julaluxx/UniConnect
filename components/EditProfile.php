<?php
// components/EditProfile.php

if ($currentUser['role'] === 'guest') {
    echo '<p class="text-red-500">กรุณาเข้าสู่ระบบเพื่อแก้ไขโปรไฟล์</p>';
    return;
}

$editError = '';
$editSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (!$username || !$email) {
        $editError = 'กรุณากรอกชื่อและอีเมลให้ครบ';
    } elseif ($password && $password !== $confirmPassword) {
        $editError = 'รหัสผ่านไม่ตรงกัน';
    } else {
        $params = [$username, $email, $bio, $currentUser['id']];
        $sql = "UPDATE users SET username=?, email=?, bio=? WHERE id=?";

        if ($password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username=?, email=?, bio=?, password=? WHERE id=?";
            $params = [$username, $email, $bio, $hashed, $currentUser['id']];
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $_SESSION['username'] = $username;

        $editSuccess = 'แก้ไขโปรไฟล์เรียบร้อยแล้ว!';
        $currentUser['username'] = $username;
        $currentUser['email'] = $email;
        $currentUser['bio'] = $bio;
    }
}

// ค่าเริ่มต้นฟอร์ม
$username = htmlspecialchars($currentUser['username'] ?? '');
$email = htmlspecialchars($currentUser['email'] ?? '');
$bio = htmlspecialchars($currentUser['bio'] ?? '');
?>

<div class="card bg-white p-4 shadow rounded w-full mx-auto">
    <h2 class="text-xl font-bold mb-4">แก้ไขโปรไฟล์</h2>

    <?php if ($editError): ?>
        <p class="text-red-500 mb-2"><?= $editError ?></p>
    <?php elseif ($editSuccess): ?>
        <p class="text-green-500 mb-2"><?= $editSuccess ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-3">
        <div>
            <label class="block text-gray-700">Username</label>
            <input type="text" name="username" value="<?= $username ?>" class="input w-full" required>
        </div>

        <div>
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" value="<?= $email ?>" class="input w-full" required>
        </div>

        <div>
            <label class="block text-gray-700">Bio</label>
            <textarea name="bio" class="textarea w-full"><?= $bio ?></textarea>
        </div>

        <div>
            <label class="block text-gray-700">รหัสผ่านใหม่ (เว้นว่างถ้าไม่เปลี่ยน)</label>
            <input type="password" name="password" class="input w-full">
        </div>

        <div>
            <label class="block text-gray-700">ยืนยันรหัสผ่านใหม่</label>
            <input type="password" name="confirm_password" class="input w-full">
        </div>

        <div class="flex justify-between mt-4">
            <button type="submit" name="edit_profile" class="btn btn-primary">บันทึก</button>
            <a href="index.php" class="btn btn-outline">ยกเลิก</a>
        </div>
    </form>
</div>