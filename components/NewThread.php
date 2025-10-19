<div class="card bg-white shadow-md p-6 mb-6">
    <h2 class="text-2xl font-bold mb-4">สร้างกระทู้ใหม่</h2>

    <?php if (!empty($threadError)): ?>
        <p class="text-red-500 mb-4"><?= htmlspecialchars($threadError) ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block mb-1 font-semibold" for="title">หัวข้อ</label>
            <input id="title" name="title" type="text" class="input input-bordered w-full" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
        </div>

        <div>
            <label class="block mb-1 font-semibold" for="category">หมวดหมู่</label>
            <select id="category" name="category_id" class="select select-bordered w-full" required>
                <option value="">-- เลือกหมวดหมู่ --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block mb-1 font-semibold" for="content">เนื้อหา (Markdown)</label>
            <textarea id="content" name="content" class="textarea textarea-bordered w-full" rows="10" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" name="new_thread" class="btn btn-primary">สร้างกระทู้</button>
        </div>
    </form>
</div>
