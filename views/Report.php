<?php
$threadId = $_GET['thread'] ?? null;
if (!$threadId) {
    echo "<div class='alert alert-error'>ไม่พบข้อมูลกระทู้</div>";
    return;
}
?>
<div class="card bg-base-100 shadow-xl p-4 mb-4">
    <h2 class="card-title">รายงานกระทู้</h2>
    <form method="POST" action="?action=report&thread=<?php echo htmlspecialchars($threadId); ?>">
        <div class="form-control mb-2">
            <label class="label">เหตุผลในการรายงาน</label>
            <textarea name="description" class="textarea textarea-bordered" placeholder="ระบุเหตุผลที่ต้องการรายงานกระทู้นี้" required></textarea>
        </div>
        <button type="submit" class="btn btn-warning">ส่งรายงาน</button>
        <a href="?thread=<?php echo htmlspecialchars($threadId); ?>" class="btn btn-ghost ml-2">ยกเลิก</a>
    </form>
</div>