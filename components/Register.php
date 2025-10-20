<div class="card bg-white p-6 mb-4 shadow rounded">
    <h2 class="text-xl font-bold mb-4">สมัครสมาชิก</h2>

    <?php if ($registerError): ?>
        <p class="text-red-500 mb-2"><?= htmlspecialchars($registerError); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
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
        มีบัญชีอยู่แล้ว? <a href="?action=login" class="text-blue-500 underline">เข้าสู่ระบบ</a>
    </p>
</div>
