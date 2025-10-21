<?php
$currentUser = $currentUser ?? ['role' => 'guest'];
$searchQuery = $searchQuery ?? '';
$action = $action ?? '';
$threadId = $threadId ?? null;
$allData = $allData ?? ['threads' => []];

// ฟังก์ชันช่วยค้นหาชื่อกระทู้
function findThreadTitle($threads, $threadId)
{
    foreach ($threads as $thread) {
        if ($thread['id'] == $threadId) {
            return $thread['title'];
        }
    }
    return 'กระทู้';
}
?>
<div class="flex flex-col mb-4">
    <!-- ช่องค้นหา -->
    <div class="form-control mb-2">
        <form action="index.php" method="GET">
            <input type="text" name="q" placeholder="ค้นหากระทู้..." class="input input-bordered w-full max-w-xs" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit" class="btn btn-primary mt-2">ค้นหา</button>
        </form>
    </div>
    <!-- Breadcrumb -->
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="index.php">หน้าหลัก</a></li>
            <?php if ($action === 'edit-profile'): ?>
                <li>แก้ไขโปรไฟล์</li>
            <?php elseif ($action === 'login'): ?>
                <li>ล็อกอิน</li>
            <?php elseif ($action === 'register'): ?>
                <li>สมัครสมาชิก</li>
            <?php elseif ($action === 'create-new-thread'): ?>
                <li>สร้างกระทู้ใหม่</li>
            <?php elseif ($action === 'manage-thread'): ?>
                <li>จัดการกระทู้</li>
            <?php elseif ($action === 'manage-user'): ?>
                <li>จัดการผู้ใช้</li>
            <?php elseif ($action === 'report' && $threadId): ?>
                <li><a href="?thread=<?php echo htmlspecialchars($threadId); ?>">กระทู้</a></li>
                <li>รายงาน</li>
            <?php elseif ($threadId): ?>
                <li><?php echo htmlspecialchars(substr(findThreadTitle($allData['threads'], $threadId), 0, 30)) . (strlen(findThreadTitle($allData['threads'], $threadId)) > 30 ? '...' : ''); ?></li>
            <?php elseif ($searchQuery): ?>
                <li>ค้นหา: <?php echo htmlspecialchars(substr($searchQuery, 0, 30)) . (strlen($searchQuery) > 30 ? '...' : ''); ?></li>
            <?php endif; ?>
        </ul>
    </div>
    <!-- ปุ่มสร้างกระทู้ -->
    <div class="flex justify-between items-center mb-2">
        <?php if ($currentUser['role'] !== 'guest'): ?>
            <a href="?action=create-new-thread" class="btn btn-primary">สร้างกระทู้ใหม่</a>
        <?php endif; ?>
    </div>
</div>