<?php
$threadId = $threadId ?? null;
?>
<div class="card bg-base-100 shadow-xl p-4 mb-4">
    <h2 class="card-title">รายงานกระทู้</h2>
    <form method="POST" action="?action=confirm-report&thread=<?php echo $threadId; ?>">
        <div class="form-control mb-2">
            <label class="label">เหตุผลในการรายงาน</label>
            <textarea name="description" class="textarea textarea-bordered" placeholder="ระบุเหตุผลที่ต้องการรายงานกระทู้นี้" required></textarea>
        </div>
        <button type="submit" class="btn btn-warning">ส่งรายงาน</button>
        <a href="?thread=<?php echo $threadId; ?>" class="btn btn-ghost ml-2">ยกเลิก</a>
    </form>
</div>