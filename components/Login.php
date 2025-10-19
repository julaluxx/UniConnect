<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit;
    } else {
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>

<div class="bg-white shadow-md rounded p-6 mb-4">
    <h2 class="text-xl font-semibold mb-4">เข้าสู่ระบบ</h2>
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
        <button type="submit" name="login" class="btn btn-primary w-full">เข้าสู่ระบบ</button>
    </form>
    <p class="text-sm text-center mt-3">
        ยังไม่มีบัญชี? <a href="index.php?action=register" class="text-blue-600">สมัครสมาชิก</a>
    </p>
</div>