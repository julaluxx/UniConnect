<div class="card bg-white p-6 mb-4 shadow rounded max-w-md mx-auto">
    <h2 class="text-xl font-bold mb-4">เข้าสู่ระบบ</h2>

    <?php if (!empty($loginError)): ?>
        <p class="text-red-500 mb-2"><?php echo htmlspecialchars($loginError); ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-4">
            <label class="block mb-1">อีเมล</label>
            <input type="email" name="email" class="input w-full border" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1">รหัสผ่าน</label>
            <input type="password" name="password" class="input w-full border" required>
        </div>

        <button type="submit" name="login" class="btn btn-primary w-full">เข้าสู่ระบบ</button>
    </form>

    <p class="mt-4 text-sm text-gray-500">
        ยังไม่มีบัญชี? <a href="?action=register" class="text-blue-500 underline">สมัครสมาชิก</a>
    </p>
</div>
