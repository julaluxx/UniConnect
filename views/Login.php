<?php
$loginError = $loginError ?? '';
?>
<div class="card bg-base-100 shadow-xl p-4 mb-4">
    <h2 class="card-title">ล็อกอิน</h2>
    <?php if ($loginError): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($loginError); ?></div>
    <?php endif; ?>
    <form method="POST" action="?action=login">
        <input type="hidden" name="login" value="1">
        <div class="form-control mb-2">
            <label class="label">อีเมล</label>
            <input type="email" name="email" class="input input-bordered" required>
        </div>
        <div class="form-control mb-2">
            <label class="label">รหัสผ่าน</label>
            <input type="password" name="password" class="input input-bordered" required>
        </div>
        <button type="submit" class="btn btn-primary">ล็อกอิน</button>
        <p class="mt-2">ยังไม่มีบัญชี? <a href="?action=register" class="link link-primary">สมัครสมาชิก</a></p>
    </form>
</div>