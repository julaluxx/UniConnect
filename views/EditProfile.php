<?php
$editError = $editError ?? '';
?>
<div class="card bg-base-100 shadow-xl p-4 mb-4">
    <h2 class="card-title">แก้ไขโปรไฟล์</h2>
    <?php if ($editError): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($editError); ?></div>
    <?php endif; ?>
    <form method="POST" action="?action=edit-profile">
        <input type="hidden" name="edit_profile" value="1">
        <div class="form-control mb-2">
            <label class="label">ชื่อผู้ใช้</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($currentUser['username']); ?>" class="input input-bordered" required>
        </div>
        <div class="form-control mb-2">
            <label class="label">อีเมล</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" class="input input-bordered" required>
        </div>
        <div class="form-control mb-2">
            <label class="label">เกี่ยวกับ</label>
            <textarea name="bio" class="textarea textarea-bordered"><?php echo htmlspecialchars($currentUser['bio'] ?? ''); ?></textarea>
        </div>
        <div class="form-control mb-2">
            <label class="label">รหัสผ่านใหม่ (ถ้าไม่เปลี่ยนให้เว้นว่าง)</label>
            <input type="password" name="password" class="input input-bordered">
        </div>
        <div class="form-control mb-2">
            <label class="label">ยืนยันรหัสผ่าน</label>
            <input type="password" name="confirm_password" class="input input-bordered">
        </div>
        <button type="submit" class="btn btn-primary">บันทึก</button>
    </form>
</div>