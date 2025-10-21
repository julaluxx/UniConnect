<?php
$threadError = $threadError ?? '';
$categories = $allData['categories'] ?? [];
?>
<div class="card bg-base-100 shadow-xl p-4 mb-4">
    <h2 class="card-title">สร้างกระทู้ใหม่</h2>
    <?php if ($threadError): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($threadError); ?></div>
    <?php endif; ?>
    <form method="POST" action="?action=create-new-thread">
        <input type="hidden" name="new_thread" value="1">
        <div class="form-control mb-2">
            <label class="label">ชื่อกระทู้</label>
            <input type="text" name="title" class="input input-bordered" required>
        </div>
        <div class="form-control mb-2">
            <label class="label">หมวดหมู่</label>
            <select name="category_id" class="select select-bordered" required>
                <option value="">เลือกหมวดหมู่</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-control mb-2">
            <label class="label">เนื้อหากระทู้</label>
            <textarea name="content" class="textarea textarea-bordered" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">สร้างกระทู้</button>
    </form>
</div>