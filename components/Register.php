<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $bio = trim($_POST['bio'] ?? '');

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->fetch()) {
        $error = "ชื่อผู้ใช้นี้ถูกใช้ไปแล้ว";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, bio) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $bio]);
        header("Location: index.php?action=login");
        exit;
    }
}
?>

<div class="bg-white shadow-md rounded p-6 mb-4">
    <h2 class="text-xl font-semibold mb-4">สมัครสมาชิก</h2>
    <?php if (!empty($error)): ?>
        <div class="text-red-500 mb-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="block mb-1">ชื่อผู้ใช้</label>
            <input type="text" name="username" required class="input input-bordered w-full" />
        </div>
        <div class="mb-3">
            <label class="block mb-1">รหัสผ่าน</label>
            <input type="password" name="password" required class="input input-bordered w-full" />
        </div>
        <div class="mb-3">
            <label class="block mb-1">คำแนะนำตัว (ไม่บังคับ)</label>
            <textarea name="bio" class="textarea textarea-bordered w-full"></textarea>
        </div>
        <button type="submit" name="register" class="btn btn-success w-full">สมัครสมาชิก</button>
    </form>
    <p class="text-sm text-center mt-3">
        มีบัญชีแล้ว? <a href="index.php?action=login" class="text-blue-600">เข้าสู่ระบบ</a>
    </p>
</div>