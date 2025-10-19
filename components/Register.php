<?php
// components/Register.php
?>

<div class="card bg-base-100 shadow-xl p-6 mb-4">
    <h2 class="text-2xl font-bold mb-4">ลงทะเบียน</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
            echo htmlspecialchars($_SESSION['error']); 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <form action="auth.php?action=register" method="POST" class="space-y-4">
        <div class="form-control">
            <label class="label" for="username">
                <span class="label-text">ชื่อผู้ใช้</span>
            </label>
            <input type="text" id="username" name="username" class="input input-bordered" required>
        </div>
        <div class="form-control">
            <label class="label" for="password">
                <span class="label-text">รหัสผ่าน</span>
            </label>
            <input type="password" id="password" name="password" class="input input-bordered" required>
        </div>
        <div class="form-control">
            <label class="label" for="confirm_password">
                <span class="label-text">ยืนยันรหัสผ่าน</span>
            </label>
            <input type="password" id="confirm_password" name="confirm_password" class="input input-bordered" required>
        </div>
        <div class="form-control">
            <label class="label" for="bio">
                <span class="label-text">ประวัติโดยย่อ</span>
            </label>
            <textarea id="bio" name="bio" class="textarea textarea-bordered" placeholder="บอกเล่าเกี่ยวกับตัวคุณ"></textarea>
        </div>
        <div class="form-control mt-6">
            <button type="submit" class="btn btn-primary">ลงทะเบียน</button>
        </div>
    </form>
    
    <p class="mt-4">
        มีบัญชีแล้ว? 
        <a href="?action=login" class="link link-primary">ล็อกอิน</a>
    </p>
</div>