<?php
$registerError = $registerError ?? '';
?>
<div class="card bg-base-100 shadow-xl p-4 mb-4">
    <h2 class="card-title">สมัครสมาชิก</h2>
    <?php if ($registerError): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($registerError); ?></div>
    <?php endif; ?>
    <form method="POST" action="?action=register">
        <input type="hidden" name="register" value="1">
        <div class="form-control mb-2">
            <label class="label">ชื่อผู้ใช้</label>
            <input type="text" name="username" class="input input-bordered" required>
        </div>
        <div class="form-control mb-2">
            <label class="label">อีเมล</label>
            <input type="email" name="email" class="input input-bordered" required>
        </div>
        <div class="form-control mb-2">
            <label class="label">รหัสผ่าน</label>
            <input type="password" name="password" class="input input-bordered" required>
        </div>
        <div class="form-control mb-2">
            <label class="label">ยืนยันรหัสผ่าน</label>
            <input type="password" name="confirm_password" class="input input-bordered" required>
        </div>
        <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
        <p class="mt-2">มีบัญชีอยู่แล้ว? <a href="?action=login" class="link link-primary">ล็อกอิน</a></p>
    </form>
</div>