<?php
// ตรวจสอบว่า $currentUser มีอยู่หรือไม่
$currentUser = $currentUser ?? [
    'username' => 'Guest',
    'role' => 'guest'
];
?>
<nav class="navbar bg-base-100 shadow-lg">
    <div class="navbar-start">
        <a href="index.php" class="btn btn-ghost text-xl">UniConnect</a>
    </div>
    <div class="navbar-center">
        <form action="index.php" method="GET" class="form-control">
            <input type="text" name="q" placeholder="ค้นหากระทู้..." class="input input-bordered w-64" value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>">
        </form>
    </div>
    <div class="navbar-end">
        <?php if ($currentUser['role'] === 'guest'): ?>
            <a href="?action=login" class="btn btn-primary mr-2">ล็อกอิน</a>
            <a href="?action=register" class="btn btn-secondary">สมัครสมาชิก</a>
        <?php else: ?>
            <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-ghost"><?php echo htmlspecialchars($currentUser['username']); ?></label>
                <ul tabindex="0" class="menu dropdown-content bg-base-100 rounded-box shadow w-52">
                    <li><a href="?action=edit-profile">แก้ไขโปรไฟล์</a></li>
                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <li><a href="?action=manage-thread">จัดการกระทู้</a></li>
                        <li><a href="?action=manage-user">จัดการผู้ใช้</a></li>
                    <?php endif; ?>
                    <li><a href="?action=logout">ออกจากระบบ</a></li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</nav>