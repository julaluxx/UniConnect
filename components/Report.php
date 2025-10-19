<?php
// components/Report.php
?>

<div class="card bg-white p-6 mb-4 mt-4 shadow rounded mx-auto w-full">
    <h2 class="text-xl font-bold mb-4">รายงานกระทู้</h2>

    <?php if ($alreadyReported): ?>
        <p class="text-red-500 mb-4">คุณได้รายงานกระทู้นี้แล้ว</p>
        <a href="?thread=<?= htmlspecialchars($threadId) ?>" class="btn btn-primary w-full">กลับไปดูกระทู้</a>
    <?php else: ?>
        <p class="mb-4">คุณต้องการรายงานกระทู้นี้หรือไม่? สามารถเพิ่มคำอธิบายสั้น ๆ ได้</p>

        <form method="POST" action="?action=confirm-report&thread=<?= htmlspecialchars($threadId) ?>" class="space-y-4">
            <div>
                <label class="block mb-1 font-semibold" for="description">คำอธิบาย (ไม่บังคับ)</label>
                <input id="description" name="description" type="text" class="input input-bordered w-full"
                    placeholder="เช่น เหตุผลที่คุณรายงานกระทู้นี้"
                    value="<?= htmlspecialchars($_POST['description'] ?? '') ?>">
            </div>

            <div class="flex flex-col gap-2">
                <button type="submit" class="btn btn-warning w-full">ยืนยันรายงาน</button>
                <a href="?thread=<?= htmlspecialchars($threadId) ?>" class="btn btn-outline w-full">ยกเลิก</a>
            </div>
        </form>
    <?php endif; ?>
</div>