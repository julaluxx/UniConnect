<?php
// components/Report.php
?>

<div class="card mx-auto mt-4 rounded shadow-lg p-6 mb-8 w-full border-2 border-yellow-500">
    <h2 class="text-2xl font-bold mb-4">รายงานกระทู้</h2>

    <?php if ($alreadyReported): ?>
        <p class="text-red-500">คุณได้รายงานกระทู้นี้แล้ว</p>
        <a href="?thread=<?= htmlspecialchars($threadId) ?>" class="btn btn-primary mt-4">กลับไปดูกระทู้</a>
    <?php else: ?>
        <p>คุณต้องการรายงานกระทู้นี้หรือไม่? สามารถเพิ่มคำอธิบายสั้น ๆ ได้</p>

        <form method="POST" action="?action=confirm-report&thread=<?= htmlspecialchars($threadId) ?>" class="mt-4 space-y-4">
            <div>
                <label class="block mb-1 font-semibold" for="description">คำอธิบาย (ไม่บังคับ)</label>
                <input 
                    id="description" 
                    name="description" 
                    type="text" 
                    class="input input-bordered w-full" 
                    value="<?= htmlspecialchars($reports['description'] ?? '') ?>" 
                    placeholder="เช่น เหตุผลที่คุณรายงานกระทู้นี้"
                >
            </div>

            <div class="flex justify-end space-x-2">
                <button type="submit" class="btn btn-warning" action="confirm-report">ยืนยันรายงาน</button>
                <a href="?thread=<?= htmlspecialchars($threadId) ?>" class="btn btn-outline">ยกเลิก</a>
            </div>
        </form>
    <?php endif; ?>
</div>
