<?php
// components/Register.php

$registerError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // ตรวจสอบรหัสผ่านตรงกัน
    if ($password !== $confirmPassword) {
        $registerError = 'รหัสผ่านไม่ตรงกัน';
    } else {
        // ตรวจสอบ email ซ้ำ
        $exists = false;
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $exists = true;
                break;
            }
        }

        if ($exists) {
            $registerError = 'อีเมลนี้ถูกใช้แล้ว';
        } else {
            // สร้าง user ใหม่ใน database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword]);

            // Login อัตโนมัติหลังสมัคร
            $_SESSION['user_id'] = $conn->lastInsertId();
            header("Location: index.php");
            exit;
        }
    }
}
?>

<div class="card bg-white p-6 mb-4 shadow rounded">
    <h2 class="text-xl font-bold mb-4">สมัครสมาชิก</h2>

    <?php if ($registerError): ?>
        <p class="text-red-500 mb-2"><?= $registerError; ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-4">
            <label class="block mb-1">ชื่อผู้ใช้</label>
            <input type="text" name="username" class="input w-full border" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">อีเมล</label>
            <input type="email" name="email" class="input w-full border" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">รหัสผ่าน</label>
            <input type="password" name="password" class="input w-full border" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">ยืนยันรหัสผ่าน</label>
            <input type="password" name="confirm_password" class="input w-full border" required>
        </div>
        <button type="submit" name="register" class="btn btn-primary w-full">สมัครสมาชิก</button>
    </form>

    <p class="mt-4 text-sm text-gray-500">
        มีบัญชีแล้ว? <a href="?action=login" class="text-blue-500 underline">เข้าสู่ระบบ</a>
    </p>
</div>
